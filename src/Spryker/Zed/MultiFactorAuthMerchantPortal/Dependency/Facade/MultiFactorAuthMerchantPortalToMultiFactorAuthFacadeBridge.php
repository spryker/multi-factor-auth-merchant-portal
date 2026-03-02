<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuthMerchantPortal\Dependency\Facade;

use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;

class MultiFactorAuthMerchantPortalToMultiFactorAuthFacadeBridge implements MultiFactorAuthMerchantPortalToMultiFactorAuthFacadeInterface
{
    /**
     * @var \Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface
     */
    protected $multiFactorAuthFacade;

    /**
     * @param \Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface $multiFactorAuthFacade
     */
    public function __construct($multiFactorAuthFacade)
    {
        $this->multiFactorAuthFacade = $multiFactorAuthFacade;
    }

    public function getAvailableUserMultiFactorAuthTypes(
        MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer
    ): MultiFactorAuthTypesCollectionTransfer {
        return $this->multiFactorAuthFacade->getAvailableUserMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);
    }

    public function getEnabledUserMultiFactorAuthTypes(
        MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer
    ): MultiFactorAuthTypesCollectionTransfer {
        return $this->multiFactorAuthFacade->getEnabledUserMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
     * @param array<int> $statuses
     *
     * @return \Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer
     */
    public function validateUserMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer,
        array $statuses = []
    ): MultiFactorAuthValidationResponseTransfer {
        return $this->multiFactorAuthFacade->validateUserMultiFactorAuthStatus($multiFactorAuthValidationRequestTransfer, $statuses);
    }

    public function validateUserCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer
    {
        return $this->multiFactorAuthFacade->validateUserCode($multiFactorAuthTransfer);
    }

    public function activateUserMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->multiFactorAuthFacade->activateUserMultiFactorAuth($multiFactorAuthTransfer);
    }

    public function deactivateUserMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->multiFactorAuthFacade->deactivateUserMultiFactorAuth($multiFactorAuthTransfer);
    }

    public function invalidateUserCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->multiFactorAuthFacade->invalidateUserCodes($multiFactorAuthTransfer);
    }
}
