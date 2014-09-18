<?php

namespace tests\unit\models;

use app\models\User;
use \app\tests\fixtures\UserFixture;
use Codeception\Util\Stub;
use yii\codeception\TestCase;
use Yii;

class UserTest extends TestCase
{
    /** @var User */
    private $_user = null;

//    public function fixtures()
//    {
//        return [
//            'user' => UserFixture::className(),
//        ];
//    }

    protected function setUp()
    {
        parent::setUp();
        // uncomment the following to load fixtures for user table
//        $this->loadFixtures(['UserFixture']);
//        User::deleteAll();
        $this->_user = new User;
    }

    public function testValidateReturnsFalseIfParametersAreNotSet() {
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
    ) {
        $this->_user->attributes = [
            'username' => $expectedUsername,
            'password' => $expectedPassword,
            'authkey' => $expectedAuthkey
        ];

        $this->assertTrue($this->_user->validate());
    }

    public function testGetIdReturnsTheExpectedId() {
        $expectedId = 123;
        $this->_user->id = $expectedId;

        $this->assertEquals($expectedId, $this->_user->getId());
    }

    /**
     * @dataProvider validUserConfigDataProvider
     */
    public function testGetAuthKeyReturnsTheExpectedAuthKey() {
        $expectedAuthkey = 'valid authkey';
        $this->_user->attributes = [
            'username' => 'valid username',
            'password' => 'valid password',
            'authkey' => $expectedAuthkey
        ];

        $this->assertEquals($expectedAuthkey, $this->_user->getAuthKey());
    }

    /**
     * @FIXME this should work with fixtures
     */
    public function testFindIdentityReturnsTheExpectedObject(
    ) {
        $this->_user->attributes = [
            'username' => 'valid user',
            'password' => 'valid password',
            'authkey' => 'valid authkey'
        ];
        $this->assertTrue($this->_user->save());

        $expectedId = $this->_user->id;

        $user = User::findIdentity($expectedId);
        $this->assertNotNull($user);
        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
    }

    /**
     * @dataProvider nonExistingIdsDataProvider
     *
     * @FIXME this should work with fixtures
     */
    public function testFindIdentityReturnsNullIfUserIsNotFound(
        $invalidId
    ) {
        $this->assertNull(User::findIdentity($invalidId));
    }

    public function nonExistingIdsDataProvider() {
        return [[-1], [null]];
    }

    /**
     * @FIXME this should work with fixtures
     */
    public function testFindIdentityByAccessTokenReturnsTheExpectedObject() {
        $expectedAccessToken = $this->generateRandomString(10);
        $this->_user->attributes = [
            'username' => 'valid username',
            'password' => 'valid password',
            'authkey' => 'valid authkey',
            'accessToken' => $expectedAccessToken
        ];
        $this->assertTrue($this->_user->save());

        $user = User::findIdentityByAccessToken($expectedAccessToken);
        $this->assertNotNull($user);
        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
    }

    /**
     * @dataProvider nonExistingAccessTokenDataProvider
     *
     * @FIXME this should work with fixtures
     */
    public function testFindIdentityByAccessTokenReturnsNullIfUserIsNotFound(
        $invalidAccessToken
    ) {
        $this->assertNull(User::findIdentityByAccessToken($invalidAccessToken));
    }

    public function nonExistingAccessTokenDataProvider() {
        return [
            ['non existing access token'], ['']
        ];
    }

    /**
     * @param int    $expectedId
     * @param string $expectedUsername
     * @param string $expectedPassword
     * @param string $expectedAuthkey
     *
     * @dataProvider validUserConfigDataProvider
     *
     * @FIXME this should work with fixtures
     */
    public function testFindByUsernameReturnsTheExpectedObject(
        $expectedId, $expectedUsername, $expectedPassword, $expectedAuthkey
    ) {
        $user = new User([
            'username' => $expectedUsername,
            'password' => $expectedPassword,
            'authkey' => $expectedAuthkey
        ]);
        $user->save();

        $user = User::findByUsername($expectedUsername);

        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
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
        $security->expects($this->any())
            ->method('validatePassword')
            ->with($expectedPassword)
            ->willReturn(true);
        $security->expects($this->any())
            ->method('generatePasswordHash')
            ->with($expectedPassword)
            ->willReturn($expectedPassword);

        Yii::$app->set('security', $security);

        $this->_user->password = Yii::$app->getSecurity()->generatePasswordHash($expectedPassword);

        $this->assertTrue($this->_user->validatePassword($expectedPassword));
    }

    /**
     * @expectedException yii\base\InvalidParamException
     */
    public function testValidatePasswordThrowsInvalidParamExceptionIfPasswordIsIncorrect() {
        $this->_user->password = 'some password';

        $this->_user->validatePassword('some other password');
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
                $this->generateRandomString(rand(1,24)), // username
                $this->generateRandomString(rand(1,128)), // password
                $this->generateRandomString(rand(1,255)) // authkey
            ];
        }
        return $output;
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
