<?php

namespace NS\TecDocSite\Pages;

use NS\ABCPApi\RestApiClients\TecDoc;
use NS\TecDocSite\Common\Helper;
use NS\TecDocSite\Common\TecDocApiConfig;
use NS\TecDocSite\Common\View;
use NS\TecDocSite\Interfaces\PageInterface;

/**
 * Главная страница
 *
 * Class Index
 * @package NS\TecDocSite\Pages
 */
class Index implements PageInterface
{
    /**
     * Возвращает html страницы
     *
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    public function getHtml()
    {
        $tecDocRestClient = new TecDoc();
        $tecDocRestClient->setTecdocHost(TecDocApiConfig::HOST)
            ->setUserKey(TecDocApiConfig::USER_KEY)
            ->setUserLogin(TecDocApiConfig::USER_LOGIN)
            ->setUserPsw(TecDocApiConfig::USER_PSW);
        $selectedLetter = !empty($_GET['letter']) ? $_GET['letter'] : '';
        $manufacturers = $tecDocRestClient->getManufacturers(Helper::getCarId());
        $manufacturersTemplateData = [];
        foreach ($manufacturers as $oneManufacturer) {
            $firstLetter = substr($oneManufacturer->name, 0, 1);
            $manufacturersTemplateData[$firstLetter][] = $oneManufacturer;
        }
        $contentTemplateData = [
            'manufacturers' => $manufacturersTemplateData,
            'carType' => Helper::getCarId(),
            'breadcrumbs' => self::getBreadcrumbs(),
            'selectedLetter' => $selectedLetter
        ];
        $content = View::deploy('manufacturers.tpl', $contentTemplateData);
        $templateData = ['content' => $content];

        return View::deploy('index.tpl', $templateData);
    }

    /**
     * Возвращает html код с хлебными крошками
     *
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    private static function getBreadcrumbs()
    {
        $templateData = [
            'breadcrumbs' => []
        ];

        return View::deploy('common/breadcumbs.tpl', $templateData);
    }
}