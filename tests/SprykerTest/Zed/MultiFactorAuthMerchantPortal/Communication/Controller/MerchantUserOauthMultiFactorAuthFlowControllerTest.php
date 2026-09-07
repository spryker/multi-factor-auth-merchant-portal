<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MultiFactorAuthMerchantPortal\Communication\Controller;

use Codeception\Test\Unit;
use ReflectionMethod;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller\MerchantUserController;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller\MerchantUserOauthMultiFactorAuthFlowController;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\MultiFactorAuthMerchantPortalCommunicationFactory;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Dependency\Client\MultiFactorAuthMerchantPortalToSessionClientInterface;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Dependency\Facade\MultiFactorAuthMerchantPortalToUserFacadeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MultiFactorAuthMerchantPortal
 * @group Communication
 * @group Controller
 * @group MerchantUserOauthMultiFactorAuthFlowControllerTest
 * Add your own group annotations below this line
 */
class MerchantUserOauthMultiFactorAuthFlowControllerTest extends Unit
{
    /**
     * @uses \Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller\MerchantUserOauthMultiFactorAuthFlowController::ROUTE_MERCHANT_USER_OAUTH_MFA
     */
    protected const string ROUTE_MERCHANT_USER_OAUTH_MFA = '/multi-factor-auth-merchant-portal/merchant-user-oauth-multi-factor-auth-flow/get-enabled-types';

    /**
     * @uses \Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller\MerchantUserOauthMultiFactorAuthFlowController::ROUTE_MERCHANT_PORTAL_DASHBOARD
     */
    protected const string ROUTE_MERCHANT_PORTAL_DASHBOARD = '/dashboard-merchant-portal-gui/dashboard';

    /**
     * @uses \Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller\MerchantUserOauthMultiFactorAuthFlowController::ROUTE_MERCHANT_PORTAL_LOGIN
     */
    protected const string ROUTE_MERCHANT_PORTAL_LOGIN = '/security-merchant-portal-gui/login';

    protected const string OAUTH_ENABLED_TYPES_TWIG = '@MultiFactorAuthMerchantPortal/MerchantUserOauthMultiFactorAuthFlow/get-enabled-types.twig';

    protected const string OAUTH_SEND_CODE_TWIG = '@MultiFactorAuthMerchantPortal/MerchantUserOauthMultiFactorAuthFlow/send-code.twig';

    /**
     * @uses \Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller\MerchantUserOauthMultiFactorAuthFlowController::MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE
     */
    protected const string MESSAGE_NO_METHOD_AVAILABLE = 'Multi-Factor Authentication is required for your account, but no verification method is currently available. Please contact site support.';

    /**
     * @uses \\Spryker\\Zed\\MultiFactorAuthMerchantPortal\\Communication\\Controller\\MerchantUserOauthMultiFactorAuthFlowController::MULTI_FACTOR_AUTH_LOGIN_USER_EMAIL_SESSION_KEY
     */
    protected const string MULTI_FACTOR_AUTH_LOGIN_EMAIL_SESSION_KEY = '_multi_factor_auth_login_user_email';

    public function testReturnGetEnabledTypesResponseRendersOauthEnabledTypesTemplate(): void
    {
        // Arrange
        $responseData = ['types' => ['EMAIL']];
        $expectedResponse = new Response();
        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['renderView'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('renderView')
            ->with(static::OAUTH_ENABLED_TYPES_TWIG, $responseData)
            ->willReturn($expectedResponse);

        // Act
        $response = (new ReflectionMethod($controllerMock, 'returnGetEnabledTypesResponse'))
            ->invoke($controllerMock, $responseData);

        // Assert
        $this->assertSame($expectedResponse, $response, 'Expected the OAuth flow to render a full page instead of the ZedUI modal.');
    }

    public function testReturnSendCodeResponseRendersOauthSendCodeTemplate(): void
    {
        // Arrange
        $responseData = ['form' => 'rendered-form'];
        $expectedResponse = new Response();
        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['renderView'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('renderView')
            ->with(static::OAUTH_SEND_CODE_TWIG, $responseData)
            ->willReturn($expectedResponse);

        // Act
        $response = (new ReflectionMethod($controllerMock, 'returnSendCodeResponse'))
            ->invoke($controllerMock, $responseData, MerchantUserController::VALIDATION_RESPONSE_SUCCESS);

        // Assert
        $this->assertSame($expectedResponse, $response, 'Expected the OAuth flow to render its own send-code page.');
    }

    public function testReturnValidationResponseRedirectsBackToOauthMultiFactorAuthStepOnError(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Act
        $response = (new ReflectionMethod($controllerMock, 'returnValidationResponse'))
            ->invoke($controllerMock, new Request(), MerchantUserController::VALIDATION_RESPONSE_ERROR, null);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(
            static::ROUTE_MERCHANT_USER_OAUTH_MFA,
            $response->getTargetUrl(),
            'Expected a failed code validation to send the merchant user back to the Multi-Factor Authentication step.',
        );
    }

    public function testReturnValidationResponseRedirectsToDashboardOnSuccess(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Act
        $response = (new ReflectionMethod($controllerMock, 'returnValidationResponse'))
            ->invoke($controllerMock, new Request(), MerchantUserController::VALIDATION_RESPONSE_SUCCESS, null);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(
            static::ROUTE_MERCHANT_PORTAL_DASHBOARD,
            $response->getTargetUrl(),
            'Expected a verified code to land the merchant user on the Merchant Portal dashboard.',
        );
    }

    public function testReturnValidationResponseRedirectsToDashboardWhenResultIsNotProvided(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Act
        $response = (new ReflectionMethod($controllerMock, 'returnValidationResponse'))
            ->invoke($controllerMock, new Request(), null, null);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(
            static::ROUTE_MERCHANT_PORTAL_DASHBOARD,
            $response->getTargetUrl(),
            'Expected the absence of an explicit error result to be treated as success.',
        );
    }

    public function testReturnSubmitAjaxFormResponseRedirectsToLoginWithAnExplanation(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addErrorMessage'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(static::MESSAGE_NO_METHOD_AVAILABLE);

        // Act
        $response = (new ReflectionMethod($controllerMock, 'returnSubmitAjaxFormResponse'))
            ->invoke($controllerMock, new Request());

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(
            static::ROUTE_MERCHANT_PORTAL_LOGIN,
            $response->getTargetUrl(),
            'Expected the OAuth flow to fall back to the login page instead of returning an AJAX payload.',
        );
    }

    public function testGetEnabledTypesActionRedirectsAwayWhenNoChallengeIsPending(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasPendingMultiFactorAuthChallenge', 'resolveNoPendingChallengeRedirectUrl'])
            ->getMock();

        $controllerMock->method('hasPendingMultiFactorAuthChallenge')->willReturn(false);
        $controllerMock->method('resolveNoPendingChallengeRedirectUrl')->willReturn(static::ROUTE_MERCHANT_PORTAL_DASHBOARD);

        // Act
        $response = $controllerMock->getEnabledTypesAction(new Request());

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(
            static::ROUTE_MERCHANT_PORTAL_DASHBOARD,
            $response->getTargetUrl(),
            'Expected the challenge page to redirect away when no Multi-Factor Authentication challenge is outstanding.',
        );
    }

    public function testResolveNoPendingChallengeRedirectUrlSendsAuthenticatedMerchantUserToDashboard(): void
    {
        // Arrange
        $controllerMock = $this->createControllerWithCurrentUser(true);

        // Act
        $url = (new ReflectionMethod($controllerMock, 'resolveNoPendingChallengeRedirectUrl'))->invoke($controllerMock);

        // Assert
        $this->assertSame(static::ROUTE_MERCHANT_PORTAL_DASHBOARD, $url);
    }

    public function testResolveNoPendingChallengeRedirectUrlSendsAnonymousVisitorToLogin(): void
    {
        // Arrange
        $controllerMock = $this->createControllerWithCurrentUser(false);

        // Act
        $url = (new ReflectionMethod($controllerMock, 'resolveNoPendingChallengeRedirectUrl'))->invoke($controllerMock);

        // Assert
        $this->assertSame(static::ROUTE_MERCHANT_PORTAL_LOGIN, $url);
    }

    protected function createControllerWithCurrentUser(bool $hasCurrentUser): MerchantUserOauthMultiFactorAuthFlowController
    {
        $userFacadeMock = $this->createMock(MultiFactorAuthMerchantPortalToUserFacadeInterface::class);
        $userFacadeMock->method('hasCurrentUser')->willReturn($hasCurrentUser);

        $factoryMock = $this->createMock(MultiFactorAuthMerchantPortalCommunicationFactory::class);
        $factoryMock->method('getUserFacade')->willReturn($userFacadeMock);

        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFactory'])
            ->getMock();
        $controllerMock->method('getFactory')->willReturn($factoryMock);

        return $controllerMock;
    }

    /**
     * @dataProvider pendingMultiFactorAuthChallengeDataProvider
     */
    public function testHasPendingMultiFactorAuthChallenge(?string $sessionValue, bool $expected, string $message): void
    {
        // Arrange
        $sessionClientMock = $this->createMock(MultiFactorAuthMerchantPortalToSessionClientInterface::class);
        $sessionClientMock->method('get')
            ->with(static::MULTI_FACTOR_AUTH_LOGIN_EMAIL_SESSION_KEY)
            ->willReturn($sessionValue);

        $factoryMock = $this->createMock(MultiFactorAuthMerchantPortalCommunicationFactory::class);
        $factoryMock->method('getSessionClient')->willReturn($sessionClientMock);

        $controllerMock = $this->getMockBuilder(MerchantUserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFactory'])
            ->getMock();
        $controllerMock->method('getFactory')->willReturn($factoryMock);

        // Act
        $hasPendingChallenge = (new ReflectionMethod($controllerMock, 'hasPendingMultiFactorAuthChallenge'))
            ->invoke($controllerMock);

        // Assert
        $this->assertSame($expected, $hasPendingChallenge, $message);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function pendingMultiFactorAuthChallengeDataProvider(): array
    {
        return [
            'session key set by the pre-auth handler' => [
                'merchant-user@example.com',
                true,
                'Expected a login awaiting its Multi-Factor Authentication code to count as a pending challenge.',
            ],
            'session key absent or already cleared' => [
                null,
                false,
                'Expected no pending challenge once the code is verified or the visitor never started a login.',
            ],
        ];
    }
}
