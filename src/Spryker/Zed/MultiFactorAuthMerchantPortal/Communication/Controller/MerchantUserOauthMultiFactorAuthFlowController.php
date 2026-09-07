<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Full-page Multi-Factor Authentication challenge for merchant users who logged in via OAuth (SSO).
 * The OAuth login is a browser redirect with no host page for the standard ZedUI modal, so this
 * controller reuses the modal flow's logic from {@link MerchantUserController} but renders full pages
 * by overriding its response seams.
 *
 * @method \Spryker\Zed\MultiFactorAuthMerchantPortal\MultiFactorAuthMerchantPortalConfig getConfig()
 * @method \Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\MultiFactorAuthMerchantPortalCommunicationFactory getFactory()
 */
class MerchantUserOauthMultiFactorAuthFlowController extends MerchantUserController
{
    /**
     * @uses \Spryker\Zed\SecurityMerchantPortalGui\Communication\Oauth\Security\Handler\OauthMerchantPortalAuthenticationSuccessHandler::ROUTE_MERCHANT_USER_OAUTH_MFA
     */
    protected const string ROUTE_MERCHANT_USER_OAUTH_MFA = '/multi-factor-auth-merchant-portal/merchant-user-oauth-multi-factor-auth-flow/get-enabled-types';

    /**
     * @uses \Spryker\Zed\SecurityMerchantPortalGui\SecurityMerchantPortalGuiConfig::MERCHANT_USER_DEFAULT_URL
     */
    protected const string ROUTE_MERCHANT_PORTAL_DASHBOARD = '/dashboard-merchant-portal-gui/dashboard';

    /**
     * @uses \Spryker\Zed\SecurityMerchantPortalGui\SecurityMerchantPortalGuiConfig::LOGIN_URL
     */
    protected const string ROUTE_MERCHANT_PORTAL_LOGIN = '/security-merchant-portal-gui/login';

    protected const string OAUTH_ENABLED_TYPES_TWIG = '@MultiFactorAuthMerchantPortal/MerchantUserOauthMultiFactorAuthFlow/get-enabled-types.twig';

    protected const string OAUTH_SEND_CODE_TWIG = '@MultiFactorAuthMerchantPortal/MerchantUserOauthMultiFactorAuthFlow/send-code.twig';

    protected const string MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE = 'Multi-Factor Authentication is required for your account, but no verification method is currently available. Please contact site support.';

    /**
     * @uses {@link \Spryker\Zed\SecurityMerchantPortalGui\Communication\Plugin\Security\Handler\MerchantUserAuthenticationSuccessHandler::MULTI_FACTOR_AUTH_LOGIN_USER_EMAIL_SESSION_KEY}
     */
    protected const string MULTI_FACTOR_AUTH_LOGIN_USER_EMAIL_SESSION_KEY = '_multi_factor_auth_login_user_email';

    /**
     * @return \Symfony\Component\HttpFoundation\Response|array<string, mixed>
     */
    public function getEnabledTypesAction(Request $request)
    {
        if ($this->hasPendingMultiFactorAuthChallenge() === false) {
            return new RedirectResponse($this->resolveNoPendingChallengeRedirectUrl());
        }

        return parent::getEnabledTypesAction($request);
    }

    protected function resolveNoPendingChallengeRedirectUrl(): string
    {
        if ($this->getFactory()->getUserFacade()->hasCurrentUser() === true) {
            return static::ROUTE_MERCHANT_PORTAL_DASHBOARD;
        }

        return static::ROUTE_MERCHANT_PORTAL_LOGIN;
    }

    protected function hasPendingMultiFactorAuthChallenge(): bool
    {
        return $this->getFactory()->getSessionClient()->get(static::MULTI_FACTOR_AUTH_LOGIN_USER_EMAIL_SESSION_KEY) !== null;
    }

    protected function returnGetEnabledTypesResponse(array $responseData): Response
    {
        return $this->renderView(static::OAUTH_ENABLED_TYPES_TWIG, $responseData);
    }

    protected function returnSendCodeResponse(array $responseData, string $responseType): Response
    {
        return $this->renderView(static::OAUTH_SEND_CODE_TWIG, $responseData);
    }

    protected function returnValidationResponse(Request $request, ?string $result = null, ?string $formName = null): Response
    {
        if ($result === static::VALIDATION_RESPONSE_ERROR) {
            return new RedirectResponse(static::ROUTE_MERCHANT_USER_OAUTH_MFA);
        }

        return new RedirectResponse(static::ROUTE_MERCHANT_PORTAL_DASHBOARD);
    }

    protected function returnSubmitAjaxFormResponse(Request $request): Response
    {
        $this->addErrorMessage(static::MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE);

        return new RedirectResponse(static::ROUTE_MERCHANT_PORTAL_LOGIN);
    }
}
