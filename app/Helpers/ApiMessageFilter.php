<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 09/02/18
 * Time: 08:41
 */

namespace App\Helpers;

/**
 * Class ApiMessageFilter
 * @package App\Helpers
 */
class ApiMessageFilter
{
    /**
     * ApiMessageFilter constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param \Exception $e
     * @return string
     */
    public function exception(\Exception $e)
    {
        $message = $e->getMessage();

        if($e->getCode() == ApiCodes::NOT_FOUND_CODE) {
            $message = "Repositório não encontrado";
        }

        return $message;
    }
}