<?php
/**
 * Description of Pimvc\Helper\Db\Field\Name\Normalize
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Helper\Db\Field\Name;

class Normalize
{

    const REMOVABLE_CHARS = [' ', '_', '/', '\\', ';', ',', '"', "'"];
    const _ANY_LATIN = 'Any-Latin';
    const _LATIN_ASCII = 'Latin-ASCII';
    const _NFD = 'NFD';
    const _NON_SPACING_MARK = '[:Nonspacing Mark:] Remove';
    const _LOWER = 'Lower()';
    const _SC = ';';
    const _DDOT = '::';
    const TRANSFOS = [
        self::_ANY_LATIN,
        self::_LATIN_ASCII,
        self::_NFD,
        self::_NON_SPACING_MARK,
        self::_LOWER,
        self::_NFD
    ];

    /**
     * normalizeFieldName
     *
     * @param string $name
     * @return string
     */
    public static function normalizeFieldName(string $name): string
    {
        $tr = \Transliterator::createFromRules(
            self::getRulesTransfos(),
            \Transliterator::FORWARD
        );
        return strtolower(
            str_replace(
                self::REMOVABLE_CHARS,
                '',
                $tr->transliterate($name)
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

    /**
     * getRulesTransfos
     *
     * @return string
     */
    private static function getRulesTransfos()
    {
        return implode(' ', array_map(function ($v) {
                return self::_DDOT . $v . self::_SC;
        }, self::TRANSFOS));
    }
}
