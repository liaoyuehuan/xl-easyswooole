<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26
 * Time: 11:26
 */

namespace App\Service\Impl;

use App\Auth\SystemApiAuth;
use App\Bean\Company;
use App\Consts\StatusConst;
use App\Http\Pagination;
use App\Model\ICompanyModel;
use App\Model\Impl\CompanyModel;
use App\Model\Impl\TokenModel;
use App\Model\Impl\UserRequestApiDayModel;
use App\Model\ITokenModel;
use App\Model\IUserRequestApiDayModel;
use App\Service\AbstractService;
use App\Service\ICompanyService;
use App\Vendor\Db\DbFactory;

class CompanyService extends AbstractService implements ICompanyService
{
    /**
     * @var ICompanyModel
     */
    private $companyModel;

    /**
     * @var IUserRequestApiDayModel
     */
    private $userRequestApiDayModel;

    /**
     * @var ITokenModel
     */
    private $tokenModel;

    /**
     * @var \MysqliDb
     */
    private $db;

    public function __construct()
    {
        $this->companyModel = CompanyModel::getInstance();
        $this->userRequestApiDayModel = UserRequestApiDayModel::getInstance();
        $this->tokenModel = TokenModel::getInstance();
        $this->db = DbFactory::getDbConnect();
    }

    function get($id): Company
    {
        return $this->companyModel->get($id);
    }

    /**
     * @param $data
     * @return string
     */
    function insert($data)
    {
        $data['status'] = StatusConst::OPEN;
        $company = $this->companyModel->createSplBeanFromData($data);
        return $this->companyModel->insert($company);
    }

    function merge($data): bool
    {
        $data['status'] = StatusConst::OPEN;
        $company = $this->companyModel->createSplBeanFromData($data);
        return $this->companyModel->merge($company);
    }

    function insertGetInsertId($data): ?string
    {
        $data['status'] = StatusConst::OPEN;
        $data['app_id'] = SystemApiAuth::generateAppId();
        $data['app_secret'] = SystemApiAuth::generateAppSecret();
        $data['password'] = SystemApiAuth::encrypt($data['password']);
        $company = $this->companyModel->createSplBeanFromData($data);
        return $this->companyModel->insertGetInsertId($company);
    }

    function select($param = []): array
    {

    }

    /**
     * @param array $param
     * @return array
     * @throws \Exception
     */
    function pagination($param = [])
    {
        $limitParam = $this->getLimitParamFromParam($param);
        $this->db->pageLimit = $limitParam->limit;
        $field = [
            'u_r_a_d.account',
            'u_r_a_d.times',
            'u_r_a_d.record_date',
            't.company_id',
            'c.name'
        ];
        if (!empty($param['company_id'])) {
            $this->db->where('company_id', $param['company_id']);
        }
        if (!empty($param['account'])) {
            $this->db->where('u_r_a_d.account', $param['account']);
        }
        return $this->db
            ->join('token t', 't.user_nick = u_r_a_d.account', 'INNER')
            ->join('company c', 'c.id = t.company_id', 'INNER')
            ->paginate(
                $this->userRequestApiDayModel->getTable() . ' u_r_a_d',
                $limitParam->page,
                $field
            );
    }

    function update($data): bool
    {

    }

    function add($data): bool
    {
    }

    function selectUserRequestApi($app_id, $param = []): array
    {
    }

    function getByName(string $name): ?Company
    {
        return $this->companyModel->getOne(function (\MysqliDb $db) use ($name) {
            $db->where('name', $name);
        });
    }

    function getByAppId(int $appId): ?Company
    {
        return $this->companyModel->getOne(function (\MysqliDb $db) use ($appId) {
            $db->where('app_id', $appId);
        });
    }

    function queryTokens(int $company_id, $param = [])
    {
        $limitParam = $this->getLimitParamFromParam($param);
        return $this->tokenModel->pagination($limitParam->page, $limitParam->limit, function (\MysqliDb $db) use ($company_id, $param) {
            $db->where('company_id', $company_id);
            if (!empty($param['account'])) {
                $db->where('user_nick', $param['account']);
            }
        });
    }

    function queryUserRequestApi($param = [])
    {
        $limitParam = $this->getLimitParamFromParam($param);
        $this->db->pageLimit = $limitParam->limit;
        $field = [
            'u_r_a_d.account',
            'u_r_a_d.times',
            'u_r_a_d.record_date',
            't.company_id',
            'c.name'
        ];
        if (!empty($param['company_id'])) {
            $this->db->where('company_id', $param['company_id']);
        }
        if (!empty($param['account'])) {
            $this->db->where('u_r_a_d.account', $param['account']);
        }
        if (!empty($param['record_date'])) {
            $this->db->where('u_r_a_d.record_date', $param['record_date']);
        }
        $this->db->pageLimit = $limitParam->limit;
        $data = $this->db
            ->join('token t', 't.user_nick = u_r_a_d.account', 'INNER')
            ->join('company c', 'c.id = t.company_id', 'INNER')
            ->paginate(
                $this->userRequestApiDayModel->getTable() . ' u_r_a_d',
                $limitParam->page,
                $field
            );
        return new Pagination($this->db->totalCount, $data);
    }

    function getByAdminId(int $admin_id): ?Company
    {
        return $this->companyModel->getOne(function (\MysqliDb $db) use ($admin_id) {
            $db->where('admin_id', $admin_id);
        });
    }

    function queryCompany($param = [])
    {
        $limitParam = $this->getLimitParamFromParam($param);
        $pagination = $this->companyModel->pagination($limitParam->page, $limitParam->limit, function (\MysqliDb $db) use ($param) {
            if (!empty($param['admin_id'])) {
                $db->where('admin_id', $param['admin_id']);
            }
            if (!empty($param['name'])) {
                $db->where('name', $param['name']);
            }
            if (!empty($param['app_id'])) {
                $db->where('app_id', $param['app_id']);
            }
        });

        array_walk($pagination->data, function (Company &$value) {
            $value->setPassword(null);
        });
        return $pagination;
    }

    function resetAppSecret(int $id): string
    {
        $data['app_secret'] = SystemApiAuth::generateAppSecret();
        $company = $this->companyModel->createSplBeanFromData($data);
        if ($this->companyModel->update($id, $company)) {
            return $data['app_secret'];
        } else {
             return null;
        }
    }


}