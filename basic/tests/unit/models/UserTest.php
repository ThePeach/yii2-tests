<?php

namespace tests\unit\models;

use app\models\User;
use app\tests\unit\fixtures\UserFixture;
use Codeception\Util\Stub;
use yii\base\InvalidParamException;
use yii\codeception\TestCase;
use Yii;

class UserTest extends TestCase
{
    /** @var User */
    private $_user = null;

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_user = new User;
    }

    public function testValidateReturnsFalseIfParametersAreNotSet()
    {
        $this->assertFalse($this->_user->validate());
    }

    /**
     * @param int    $expectedId
     * @param string $expectedUsername
     * @param string $expectedPassword
     * @param string $expectedAuthkey
     *
     * @dataProvider validUserConfigDataProvider
     */
    public function testValidateReturnsTrueIfParametersAreSet(
        $expectedId, $expectedUsername, $expectedPassword, $expectedAuthkey
    )
    {
        $this->_user->attributes = [
            'username' => $expectedUsername,
            'password' => $expectedPassword,
            'authkey' => $expectedAuthkey
        ];

        $this->assertTrue($this->_user->validate());
    }

    public function testGetIdReturnsTheExpectedId()
    {
        $expectedId = 123;
        $this->_user->id = $expectedId;

        $this->assertEquals($expectedId, $this->_user->getId());
    }

    public function testGetAuthKeyReturnsTheExpectedAuthKey()
    {
        $expectedAuthkey = 'valid authkey';
        $this->_user->authkey = $expectedAuthkey;

        $this->assertEquals($expectedAuthkey, $this->_user->getAuthKey());
    }

    /**
     * @dataProvider validFixturesKeysDataProvider
     */
    public function testFindIdentityReturnsTheExpectedObject($fixtureKey) {
        $expectedId = $this->user[$fixtureKey]['id'];

        /** @var User $user */
        $user = User::findIdentity($expectedId);
        $this->assertNotNull($user);
        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
        $this->assertEquals($expectedId, $user->id);
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
     *
     * @FIXME this should work with fixtures
     */
    public function testFindByUsernameReturnsNullIfUserNotFound(
        $invalidUsername
    ) {
        $this->assertNull(User::findByUsername($invalidUsername));
    }

    public function nonExistingUsernamesDataProvider() {
        return [[3], [-1], [null], ['not found']];
    }

    public function testValidateAuthkeyReturnsFalseIfAuthkeyIsDifferent() {
        $this->_user->authkey = 'some auth key';

        $this->assertFalse($this->_user->validateAuthKey('wrong auth key'));
    }

    public function testValidateAuthkeyReturnsTrueIfAuthkeyIsEqual() {
        $expectedAuthkey = 'valid auth key';
        $this->_user->authkey = $expectedAuthkey;

        $this->assertTrue($this->_user->validateAuthKey($expectedAuthkey));
    }


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
            ['user_basic'], ['admin'], ['user_accessToken'], ['user_id']
        ];
    }

    /**
     * @param string $expectedPassword the password used for encoding
     *                                 also used for validating if the second parameter is not set
     * @param mixed $wrongPassword     if passed, validatePassword will throw exception InvalidParamException
     *                                 when presenting this pass
     */
    private function _mockYiiSecurity($expectedPassword, $wrongPassword = false)
    {
        // @FIXME the following doesn't work!! :-(
//        $security = Stub::construct(
//            'yii\base\Security',
//            [
//                'validatePassword' => true,
//                'generatePasswordHash' => $expectedPassword
//            ]
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

    /**
     * valid User config Data Provider.
     *
     * @return array
     */
    public function validUserConfigDataProvider() {
        $output = [];
        for ($i = 0; $i < 2; $i++) {
            $output[] = [
                rand(1,999), // id
                $this->_generateRandomString(rand(1,24)), // username
                $this->_generateRandomString(rand(1,128)), // password
                $this->_generateRandomString(rand(1,255)) // authkey
            ];
        }
        return $output;
    }

    private function _generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
