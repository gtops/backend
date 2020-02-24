<?php
namespace App\Services\Organization;

use App\Domain\Models\IRepository;
use App\Domain\Models\Organization;
use App\Domain\Models\IModel;

class OrganizationService
{
    private $organizationRepository;
    public function __construct(IRepository $orgRep)
    {
        $this->organizationRepository = $orgRep;
    }

    public function addOrganization(Organization $organization)
    {
        return $this->organizationRepository->add($organization);
    }

    public function deleteOrganization(int $id)
    {
        $this->organizationRepository->delete($id);
    }

    public function getOrganization(int $id):?IModel
    {
        return $this->organizationRepository->get($id);
    }

    /**
     * @return IModel[]
     */
    public function getOrganizations():?array
    {
        return $this->organizationRepository->getAll();
    }

    public function update(Organization $organization)
    {
        $this->organizationRepository->update($organization);
    }
}