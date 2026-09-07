<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Form\DataProvider;

use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Controller\MerchantUserController;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Reader\Request\RequestReaderInterface;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Communication\Reader\User\UserReaderInterface;
use Spryker\Zed\MultiFactorAuthMerchantPortal\Dependency\Facade\MultiFactorAuthMerchantPortalToMultiFactorAuthFacadeInterface;
use Symfony\Component\HttpFoundation\Request;

class TypeSelectionFormDataProvider
{
    public function __construct(
        protected MultiFactorAuthMerchantPortalToMultiFactorAuthFacadeInterface $multiFactorAuthFacade,
        protected UserReaderInterface $userReader,
        protected RequestReaderInterface $requestReader
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(Request $request): array
    {
        return [
            MerchantUserController::TYPES => $this->getTypes($request),
            MerchantUserController::IS_ACTIVATION => false,
            MerchantUserController::IS_DEACTIVATION => false,
            MerchantUserController::TYPE_TO_SET_UP => null,
            MerchantUserController::MODAL_FORM_SELECTOR_PARAMETER => $this->requestReader->get($request, MerchantUserController::MODAL_FORM_SELECTOR_PARAMETER),
            MerchantUserController::MODAL_IS_LOGIN_PARAMETER => $this->requestReader->get($request, MerchantUserController::MODAL_IS_LOGIN_PARAMETER),
            MerchantUserController::MODAL_AJAX_FORM_SELECTOR_PARAMETER => $this->requestReader->get($request, MerchantUserController::MODAL_AJAX_FORM_SELECTOR_PARAMETER),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getTypes(Request $request): array
    {
        $userTransfer = $this->userReader->getUser();

        if ($this->isActivationFlow($request, $userTransfer)) {
            return [$this->requestReader->get($request, MerchantUserController::TYPE_TO_SET_UP)];
        }

        $multiFactorAuthCriteraTransfer = (new MultiFactorAuthCriteriaTransfer())->setUser($userTransfer);
        $multiFactorAuthTypesCollectionTransfer = $this->multiFactorAuthFacade->getEnabledUserMultiFactorAuthTypes($multiFactorAuthCriteraTransfer);
        $types = [];

        foreach ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes() as $multiFactorAuthTypeTransfer) {
            $types[] = $multiFactorAuthTypeTransfer->getTypeOrFail();
        }

        return $types;
    }

    protected function isActivationFlow(
        Request $request,
        UserTransfer $userTransfer
    ): bool {
        $multiFactorAuthValidationRequestTransfer = (new MultiFactorAuthValidationRequestTransfer())->setUser($userTransfer);

        return $this->requestReader->get($request, MerchantUserController::IS_ACTIVATION) &&
            $this->multiFactorAuthFacade->validateUserMultiFactorAuthStatus($multiFactorAuthValidationRequestTransfer)->getIsRequired() === false;
    }
}
