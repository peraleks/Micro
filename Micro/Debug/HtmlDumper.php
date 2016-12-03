<?php
namespace MicroMir\Debug;

use Symfony\Component\VarDumper\Dumper\HtmlDumper as SymfonyHtmlDumper;

class HtmlDumper extends SymfonyHtmlDumper
{
    /**
     * @var array
     */
    protected $styles = [
        'num'       => 'color:#68ace0',
        'const'     => 'font-weight:bold',
        'str'       => 'color:#8fdf8e',
        'note'      => 'color:#dfdfa4',
        'ref'       => 'color:#999',
        'public'    => 'color:#fc9a48',
        'protected' => 'color:#fc9a48',
        'private'   => 'color:#fc9a48',
        'meta'      => 'color:#7e65ff',
        'key'       => 'color:#c18ddd',
        'index'     => 'color:#c18ddd',
        'cchr'      => 'color:#999',
        'default'   => 'background-color:#232525;
                        color:#888; line-height:1.2em;
                        font-size:14px;
                        font-family: Consolas, Menlo, Monaco, monospace;
                        word-wrap: break-word;
                        white-space: pre-wrap;
                        position:relative;
                        z-index:0;
                        word-break: normal;
                        border-radius:0 0 5px 5px;
                        margin: 0em 0px 1em;'
    ];

}
