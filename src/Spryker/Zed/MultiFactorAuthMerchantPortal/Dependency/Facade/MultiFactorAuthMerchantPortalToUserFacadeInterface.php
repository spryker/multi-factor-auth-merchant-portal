<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuthMerchantPortal\Dependency\Facade;

use Generated\Shared\Transfer\UserCollectionTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface MultiFactorAuthMerchantPortalToUserFacadeInterface
{
    public function hasCurrentUser(): bool;

    public function getCurrentUser(): UserTransfer;

    public function getUserCollection(UserCriteriaTransfer $userCriteriaTransfer): UserCollectionTransfer;
}
