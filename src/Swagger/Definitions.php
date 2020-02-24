<?php
/**
 * @SWG\Definition(
 *    definition="OrganizationResponse",
 *    type="object",
 *    @SWG\Property(property="id", type="integer"),
 *    @SWG\Property(property="name", type="string"),
 *    @SWG\Property(property="address", type="string"),
 *    @SWG\Property(property="leader", type="string"),
 *    @SWG\Property(property="phoneNumber", type="string"),
 *    @SWG\Property(property="OQRN", type="string"),
 *    @SWG\Property(property="payment_account", type="string"),
 *    @SWG\Property(property="branch", type="string"),
 *    @SWG\Property(property="bik", type="string"),
 *    @SWG\Property(property="correspondent_account", type="string"),
 * )
*/

/**
 * @SWG\Definition(
 *    definition="OrganizationRequest",
 *    type="object",
 *    @SWG\Property(property="name", type="string"),
 *    @SWG\Property(property="address", type="string"),
 *    @SWG\Property(property="leader", type="string"),
 *    @SWG\Property(property="phoneNumber", type="string"),
 *    @SWG\Property(property="oqrn", type="string"),
 *    @SWG\Property(property="paymentAccount", type="string"),
 *    @SWG\Property(property="branch", type="string"),
 *    @SWG\Property(property="bik", type="string"),
 *    @SWG\Property(property="correspondentAccount", type="string"),
 * )
 */

/**
 * @SWG\Definition(
 *    definition="LocalAdminRequest",
 *    type="object",
 *    @SWG\Property(property="name", type="string"),
 *    @SWG\Property(property="password", type="string"),
 *    @SWG\Property(property="email", type="string"),
 * )
 */

/**
 * @SWG\Definition(
 *    definition="LocalAdminResponse",
 *    type="object",
 *    @SWG\Property(property="userId", type="integer"),
 *    @SWG\Property(property="name", type="string"),
 *    @SWG\Property(property="email", type="string"),
 *    @SWG\Property(property="roleId", type="integer"),
 *    @SWG\Property(property="isActivity", type="string"),
 *    @SWG\Property(property="registrationDate", type="string"),
 *    @SWG\Property(property="organizationId", type="string"),
 *    @SWG\Property(property="localAdminId", type="integer")
 * )
 */