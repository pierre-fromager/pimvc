<?php
namespace Pimvc\Db\Charset;

trait Convert
{
      /**
     * getUtf8To16Le
     *
     * @param string $value
     * @return string
     */
    protected function getUtf8To16(string $value): string
    {
        return $this->getCharsetConvert($value, 'utf-8', 'utf-16');
    }

    /**
     * getUtf16LeTo8
     *
     * @param string $value
     * @return string
     */
    protected function getUtf16To8(string $value): string
    {
        return $this->getCharsetConvert($value, 'utf-16', 'utf-8');
    }

    /**
     * getCharsetConvertValue
     *
     * @param string $v
     * @param string $cf
     * @param string $ct
     * @return string
     */
    protected function getCharsetConvert(string $v, string $cf = 'utf-16', string $ct = 'utf-8'): string
    {
        $cv = @iconv($cf, $ct, $v);
        return ($cv === false) ? $v : (string) $cv;
    }

    /**
     * utfConvert
     *
     * @param array $aa
     * @param string $cf
     * @param string $ct
     */
    protected function charsetConvert(array &$aa, string $cf = 'utf-16', string $ct = 'utf-8')
    {
        \array_walk($aa, function (&$v) use ($cf, $ct) {
            if (!is_numeric($v) && !is_null($v)) {
                $v = $this->getCharsetConvert($v, $cf, $ct);
            }
        });
    }

    /**
     * utfConvertCollection
     *
     * @param array $aac
     * @param string $cf
     * @param string $ct
     */
    protected function charsetConvertCollection(array &$aac, string $cf = 'utf-16', string $ct = 'utf-8')
    {
        $counter = count($aac);
        for ($c = 0; $c < $counter; ++$c) {
            $this->charsetConvert($aac[$c], $cf, $ct);
        }
    }
}
