<?php
/**
 * Description of Pimvc\Helper\Db\Field\Name\Normalize
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Helper\Db\Field\Name;

class Normalize
{

    const REMOVABLE_CHARS = [' ', '_', '/', '\\', ';', ',', '"', "'",];

    /**
     * normalizeFieldName
     *
     * @param string $name
     * @return string
     */
    public static function normalizeFieldName(string $name): string
    {
        return strtolower(
            str_replace(
                self::REMOVABLE_CHARS,
                '',
                iconv('UTF-8', 'ASCII//IGNORE', $name)
            )
        );
    }

    /**
     * normalizeFieldsName
     *
     * @param array $nameCollection
     * @return array
     */
    public static function normalizeFieldsName(array $nameCollection): array
    {
        $fieldsName = [];
        $count = count($nameCollection);
        for ($c = 0; $c < $count; ++$c) {
            $fieldsName[] = self::normalizeFieldName($nameCollection[$c]);
        }
        return $fieldsName;
    }
}
