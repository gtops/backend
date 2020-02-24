<?php
namespace App\Persistance\Repositories\Organization;
use \App\Domain\Models\Organization;
use App\Persistance\ModelsEloquant\Organization\Organization as OrgPDO;

class OrganizationRepository implements \App\Domain\Models\IRepository
{

    public function get(int $id): ?\App\Domain\Models\IModel
    {
        $organization = OrgPDO::query()->where('organization_id', '=', $id)->get();
        if (count($organization) == 0){
            return null;
        }

        return new Organization(
            $organization[0]['organization_id'],
            $organization[0]['name'],
            $organization[0]['address'],
            $organization[0]['leader'],
            $organization[0]['phone_number'],
            $organization[0]['OQRN'],
            $organization[0]['payment_account'],
            $organization[0]['branch'],
            $organization[0]['bik'],
            $organization[0]['correspondent_account']
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?array
    {
        $organizations = OrgPDO::query()->get();

        if (count($organizations) == 0){
            return null;
        }

        $organizationsForResponse = [];
        foreach ($organizations as $organization){
            $organizationsForResponse[] = new Organization
            (
                $organization['organization_id'],
                $organization['name'],
                $organization['address'],
                $organization['leader'],
                $organization['phone_number'],
                $organization['OQRN'],
                $organization['payment_account'],
                $organization['branch'],
                $organization['bik'],
                $organization['correspondent_account']
            );
        }

        return $organizationsForResponse;

    }

    /**@var $model Organization*/
    public function add(\App\Domain\Models\IModel $model):int
    {
        $modelInArray = $model->toArray();
        unset($modelInArray['id']);
        $id = OrgPDO::query()->create($modelInArray);
        return $id->getAttribute('organization_id');
    }

    public function delete(int $id)
    {
        OrgPDO::query()->where('organization_id', '=', $id)->delete();
    }

    public function update(Organization $organization)
    {
        OrgPDO::query()->where('organization_id', '=', $organization->getId())->update([
            'name' => $organization->getName(),
            'address' => $organization->getAddress(),
            'leader' => $organization->getLeader(),
            'phone_number' => $organization->getPhoneNumber(),
            'OQRN' => $organization->getOqrn(),
            'payment_account' => $organization->getPaymentAccount(),
            'branch' => $organization->getBranch(),
            'bik' => $organization->getBik(),
            'correspondent_account' => $organization->getCorrespondentAccount()
        ]);
    }
}