<?php

namespace App\Controllers;

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Shavian extends BaseController
{

    public function index(): string
    {
        $data = [
            'page_title' => '𐑖𐑱𐑝𐑾𐑯',
            'slug_group' => 'shavian',
            'slug'       => '/office/shavian',
        ];
        return view('shavian', $data);
    }

    public function toIpa(): string
    {
        $data = [
            'page_title' => '𐑖𐑱𐑝𐑾𐑯-IPA',
            'slug_group' => 'shavian',
            'slug'       => '/office/shavian-ipa',
        ];
        return view('shavian-ipa', $data);
    }

    public function keyboard(): string
    {
        $data = [
            'page_title' => '𐑖𐑱𐑝𐑾𐑯 𐑒𐑰𐑚𐑹𐑛',
            'slug_group' => 'shavian',
            'slug'       => '/office/shavian-keyboard',
        ];
        return view('shavian-keyboard', $data);
    }

    public function ajaxTranslator(): ResponseInterface
    {
        helper('math');
        $mode = $this->request->getPost('mode');
        if ('transcribe' == $mode) {
            $text        = $this->request->getPost('text');
            $error       = '-';
            $transcribed = '-';
            $file        = '-';
            if (empty($text)) {
                $text    = '-';
                $error   = 'Sorry, the text is empty.';
            } else {
                $file = 'dictionary/sh_to_en.php';
                $pattern = '/[A-Za-z]+/';
                if (preg_match($pattern, $text) === 1) {
                    $file = 'dictionary/en_to_sh.php';
                }
                $transcribed = transcribeShavian($text, $file);
            }
            return $this->response->setJSON([
                'mode'                => 'transcribe',
                'original_message'    => $text,
                'transcribed_message' => $transcribed,
                'error'               => $error,
                'file'                => $file,
            ]);
        } else if ('ipa' == $mode) {
            $text        = $this->request->getPost('text');
            $converted   = '-';
            $error       = '-';
            if (empty($text)) {
                $text    = '-';
                $error   = 'Sorry, the text is empty.';
            } else {
                $converter = [
                    "𐑐" => "p",
                    "𐑑" => "t",
                    "𐑒" => "k",
                    "𐑓" => "f",
                    "𐑔" => "θ",
                    "𐑕" => "s",
                    "𐑖" => "ʃ",
                    "𐑗" => "ʧ",
                    "𐑘" => "j",
                    "𐑙" => "ŋ",
                    "𐑚" => "b",
                    "𐑛" => "d",
                    "𐑜" => "ɡ",
                    "𐑝" => "v",
                    "𐑞" => "ð",
                    "𐑟" => "z",
                    "𐑠" => "ʒ",
                    "𐑡" => "ʤ",
                    "𐑢" => "w",
                    "𐑣" => "h",
                    "𐑤" => "l",
                    "𐑮" => "r",
                    "𐑥" => "m",
                    "𐑯" => "n",
                    "𐑦" => "ɪ",
                    "𐑰" => "iː",
                    "𐑧" => "ɛ",
                    "𐑱" => "eɪ",
                    "𐑨" => "æ",
                    "𐑲" => "aɪ",
                    "𐑩" => "ə",
                    "𐑳" => "ʌ",
                    "𐑪" => "ɒ",
                    "𐑴" => "əʊ",
                    "𐑫" => "ʊ",
                    "𐑵" => "uː",
                    "𐑬" => "aʊ",
                    "𐑶" => "ɔɪ",
                    "𐑭" => "ɑː",
                    "𐑷" => "ɔː",
                    "𐑸" => "ɑː(r)",
                    "𐑹" => "ɔː(r)",
                    "𐑺" => "ɛə(r)",
                    "𐑻" => "ɜː(r)",
                    "𐑼" => "ə(r)",
                    "𐑽" => "ɪə(r)",
                    "𐑾" => "ɪə",
                    "𐑿" => "ju(ː)"
                ];
                $str_len   = mb_strlen($text);
                $converted = '';
                for ($i = 0; $i < $str_len; $i++) {
                    $char = mb_substr($text, $i, 1);
                    if (isset($converter[$char])) {
                        $converted .= $converter[$char];
                    } else {
                        $converted .= $char;
                    }
                }
            }
            return $this->response->setJSON([
                'mode'              => 'ipa',
                'original_message'  => $text,
                'converted_message' => $converted,
                'error'             => $error
            ]);
        }
        return $this->response->setJSON([]);
    }
}