<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

/**
 * AttributeRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttributeRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByLangAndShop($idLang, $idShop)
    {
        $attributeGroups = [];

        $qb = $this->createQueryBuilder('a')
            ->addSelect('ag.id AS attributeGroupId')
            ->addSelect('ag.position AS attributeGroupPosition')
            ->addSelect('agl.name AS attributeGroupName')
            ->addSelect('agl.publicName AS attributeGroupPublicName')
            ->addSelect('a.id')
            ->addSelect('a.color')
            ->addSelect('a.position as attributePosition')
            ->addSelect('al.name AS attributeName')
            ->join('a.attributeGroup', 'ag')
            ->join('a.shops', 's')
            ->join('a.attributeLangs', 'al')
            ->join('ag.attributeGroupLangs', 'agl')
            ->where('al.lang = :idLang')
            ->andWhere('agl.lang = :idLang')
            ->andWhere('s.id = :idShop')
            ->orderBy('attributePosition')
            ->addOrderBy('attributeGroupPosition')
            ->setParameters([
                'idShop' => $idShop,
                'idLang' => $idLang,
            ]);

        $result = $qb->getQuery()->getArrayResult();

        foreach ($result as $attribute) {
            if (isset($attributeGroups[$attribute['attributeGroupPosition']])) {
                $attributeGroups[$attribute['attributeGroupPosition']]['attributes'][$attribute['attributePosition']] = $this->getAttributeRow($attribute);
            } else {
                $attributeGroups[$attribute['attributeGroupPosition']] = [
                    'id' => $attribute['attributeGroupId'],
                    'name' => $attribute['attributeGroupName'],
                    'publicName' => $attribute['attributeGroupPublicName'],
                    'position' => $attribute['attributeGroupPosition'],
                    'attributes' => [
                        $attribute['attributePosition'] => $this->getAttributeRow($attribute),
                    ],
                ];
            }
        }

        return $attributeGroups;
    }

    private function getAttributeRow($attribute)
    {
        $attributes = [
            'id' => $attribute['id'],
            'color' => $attribute['color'],
            'position' => $attribute['attributePosition'],
            'name' => $attribute['attributeName'],
            'texture' => '',
        ];
        if (@file_exists(_PS_COL_IMG_DIR_ . $attribute['id'] . '.jpg')) {
            $attributes['texture'] = _THEME_COL_DIR_ . $attribute['id'] . '.jpg';
        }

        return $attributes;
    }
}
