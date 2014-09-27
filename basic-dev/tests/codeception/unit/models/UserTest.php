<?php

namespace tests\codeception\unit\models;

use app\models\User;
use app\tests\codeception\unit\fixtures\UserFixture;
use Codeception\Util\Stub;
use yii\base\InvalidParamException;
use yii\codeception\TestCase;
use Yii;

class UserTest extends TestCase
{
    /** @var User */
    private $_user = null;

    public function globalFixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }

    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@app/tests/codeception/unit/fixtures/data/userModels.php'
            ]
        ];
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_user = new User;
    }

    /* VALIDATION RULES */

    public function testValidateReturnsFalseIfParametersAreNotSet()
    {
        $this->assertFalse($this->_user->validate());
    }

    public function testValidateReturnsTrueIfParametersAreSet()
    {
        $this->_user->attributes = [
            'username' => 'a valid username',
            'password' => 'a valid password',
            'authkey' => 'a valid authkey'
        ];

        $this->assertTrue($this->_user->validate());
    }

    /* getId() */

    public function testGetIdReturnsTheExpectedId()
    {
        $expectedId = 123;
        $this->_user->id = $expectedId;

        $this->assertEquals($expectedId, $this->_user->getId());
    }

    /* getAuthKey() */

    public function testGetAuthKeyReturnsTheExpectedAuthKey()
    {
        $expectedAuthkey = 'valid authkey';
        $this->_user->authkey = $expectedAuthkey;

        $this->assertEquals($expectedAuthkey, $this->_user->getAuthKey());
    }

    /* findIdentity() */

    /**
     * @dataProvider validFixturesKeysDataProvider
     */
    public function testFindIdentityReturnsTheExpectedObject($fixtureKey) {
        $expectedAttrs = $this->user[$fixtureKey];

        /** @var User $user */
        $user = User::findIdentity($expectedAttrs['id']);

        $this->assertNotNull($user);
        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
        $this->assertEquals($expectedAttrs['id'], $user->id);
        $this->assertEquals($expectedAttrs['username'], $user->username);
        $this->assertEquals($expectedAttrs['password'], $user->password);
        $this->assertEquals($expectedAttrs['authkey'], $user->authkey);
    }

    /**
     * @dataProvider nonExistingIdsDataProvider
     */
    public function testFindIdentityReturnsNullIfUserIsNotFound(
        $invalidId
    ) {
        $this->assertNull(User::findIdentity($invalidId));
    }

    public function nonExistingIdsDataProvider() {
        return [[-1], [null], [300]];
    }

    /* findIdentityByAccessToken() */

    public function testFindIdentityByAccessTokenReturnsTheExpectedObject()
    {
        $expectedAccessToken = $this->user['user_accessToken']['accessToken'];

        /** @var User $user */
        $user = User::findIdentityByAccessToken($expectedAccessToken);
        $this->assertNotNull($user);
        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
        $this->assertEquals($expectedAccessToken, $user->accessToken);
    }

    /**
     * @dataProvider nonExistingAccessTokenDataProvider
     */
    public function testFindIdentityByAccessTokenReturnsNullIfUserIsNotFound(
        $invalidAccessToken
    ) {
        $this->assertNull(User::findIdentityByAccessToken($invalidAccessToken));
    }

    public function nonExistingAccessTokenDataProvider() {
        return [
            [null], ['non existing access token'], ['']
        ];
    }

    /* findByUsername() */

    /**
     * @dataProvider validFixturesKeysDataProvider
     */
    public function testFindByUsernameReturnsTheExpectedObject($fixtureKey)
    {
        $expectedUsername = $this->user[$fixtureKey]['username'];

        /** @var User $user */
        $user = User::findByUsername($expectedUsername);

        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
        $this->assertEquals($expectedUsername, $user->username);
    }

    /**
     * @dataProvider nonExistingUsernamesDataProvider
     */
    public function testFindByUsernameReturnsNullIfUserNotFound(
        $invalidUsername
    ) {
        $this->assertNull(User::findByUsername($invalidUsername));
    }

    public function nonExistingUsernamesDataProvider() {
        return [[3], [-1], [null], ['not found']];
    }

    /* validateAuthkey() */

    public function testValidateAuthkeyReturnsFalseIfAuthkeyIsDifferent() {
        $this->_user->authkey = 'some auth key';

        $this->assertFalse($this->_user->validateAuthKey('wrong auth key'));
    }

    public function testValidateAuthkeyReturnsTrueIfAuthkeyIsEqual() {
        $expectedAuthkey = 'valid auth key';
        $this->_user->authkey = $expectedAuthkey;

        $this->assertTrue($this->_user->validateAuthKey($expectedAuthkey));
    }

    /* validatePassword() */

    public function testValidatePasswordReturnsTrueIfPasswordIsCorrect() {
        $expectedPassword = 'valid password';
        $this->_mockYiiSecurity($expectedPassword);

        $this->_user->password = Yii::$app->getSecurity()->generatePasswordHash($expectedPassword);

        $this->assertTrue($this->_user->validatePassword($expectedPassword));
    }

    /**
     * @expectedException yii\base\InvalidParamException
     */
    public function testValidatePasswordThrowsInvalidParamExceptionIfPasswordIsIncorrect() {
        $password = 'some password';
        $otherPassword = 'some other password';
        $this->_mockYiiSecurity($password, $otherPassword);

        $this->_user->password = $password;
        $this->_user->validatePassword($otherPassword);
    }

    public function validFixturesKeysDataProvider()
    {
        return [
            ['user_basic'], ['user_accessToken'], ['user_id']
        ];
    }

    /**
     * Mocks the Yii Security module so we can make it return what we need
     *
     * @param string $expectedPassword the password used for encoding
     *                                 also used for validating if the second parameter is not set
     * @param mixed $wrongPassword     if passed, validatePassword will throw exception InvalidParamException
     *                                 when presenting this pass
     */
    private function _mockYiiSecurity($expectedPassword, $wrongPassword = false)
    {
        // @FIXME the following doesn't work!! :-(
//        $configuration = [
//            'generatePasswordHash' => $expectedPassword
//        ];
//        if ($wrongPassword) {
//            $configuration['validatePassword'] = function () { throw new InvalidParamException(); };
//        }
//        else {
//            $configuration['validatePassword'] = true;
//        }
//        $security = Stub::construct(
//            'yii\base\Security',
//            $configuration
//        );

        $security = $this->getMock(
            'yii\base\Security',
            ['validatePassword', 'generatePasswordHash']
        );
        if ($wrongPassword) {
            $security->expects($this->any())
                ->method('validatePassword')
                ->with($wrongPassword)
                ->willThrowException(new InvalidParamException());
        } else {
            $security->expects($this->any())
                ->method('validatePassword')
                ->with($expectedPassword)
                ->willReturn(true);
        }
        $security->expects($this->any())
            ->method('generatePasswordHash')
            ->with($expectedPassword)
            ->willReturn($expectedPassword);

        Yii::$app->set('security', $security);
    }
}
