<?php

namespace Microwin7\PHPUtils;

if (!function_exists('str_starts_with_slash')) {
    function str_starts_with_slash(string $string, bool $needle_starts_with_slash = FALSE): string
    {
        if (str_starts_with($string, DIRECTORY_SEPARATOR)) {
            if (!$needle_starts_with_slash) $string =  substr($string, 1);
        } else {
            if ($needle_starts_with_slash) $string =  DIRECTORY_SEPARATOR . $string;
        }
        return $string;
    }
}
if (!function_exists('str_ends_with_slash')) {
    function str_ends_with_slash(string $string, bool $needle_ends_with_slash = TRUE): string
    {
        if (str_ends_with($string, DIRECTORY_SEPARATOR)) {
            if (!$needle_ends_with_slash) $string =  substr($string, 0, -1);
        } else {
            if ($needle_ends_with_slash) $string .=  DIRECTORY_SEPARATOR;
        }
        return $string;
    }
}
if (!function_exists('ar_slash_string')) {
    function ar_slash_string(string $string, bool $needle_starts_with_slash = FALSE, bool $needle_ends_with_slash = TRUE): string
    {
        return str_ends_with_slash(str_starts_with_slash($string, $needle_starts_with_slash), $needle_ends_with_slash);
    }
}
if (!function_exists('implodeRecursive')) {
    /**
     * @param string $separator
     * @param string[]|list<string|list<string>> $array
     * @return string
     */
    function implodeRecursive(string $separator, array $array): string
    {
        $result = '';
        foreach ($array as $value) {
            if (is_array($value)) {
                $result .= implodeRecursive($separator, $value) . $separator;
            } else {
                if (strrpos($value, '\\') === false) {
                    $result .= $value . $separator;
                } else if (enum_exists($value)) {
                    $argumentClazz = new \ReflectionClass($value);
                    if ($argumentClazz->implementsInterface(\Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface::class)) {
                        /** 
                         * @var interface-string<
                         *     \BackedEnum & 
                         *     \Microwin7\PHPUtils\Contracts\Enum\EnumInterface & 
                         *     \Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface
                         * > $enumClass 
                         */
                        $enumClass = $value;
                        $result .= $enumClass::getNameRequestVariable() . $separator;
                    }
                }
            }
        }

        return rtrim($result, $separator);
    }
}
if (!function_exists('getClassMethodsAllFromDocComment')) {
    /**
     * @param class-string|object|trait-string $class
     * 
     * @return string[]|null
     */
    function getClassMethodsAllFromDocComment(string|object $class): ?array
    {
        $reflectionClass = new \ReflectionClass($class);
        $docComment = $reflectionClass->getDocComment();
        if ($docComment !== false) {
            preg_match_all('/@method\s+([^\r\n\t\f\v(]+)/', $docComment, $matches);
            if (!empty($matches[1])) {
                return $matches[1];
            }
        }
        return null;
    }
}
if (!function_exists('getClassMethodsFromAnnotations')) {
    /**
     * @param class-string|object|trait-string $class
     * 
     * @return string[]
     */
    function getClassMethodsFromAnnotations(string|object $class): array
    {
        $annotations = [];
        $methodsDocComment = getClassMethodsAllFromDocComment($class);
        if ($methodsDocComment !== null) {
            foreach ($methodsDocComment as $v) {
                $methodComment = explode(" ", $v);
                if (count($methodComment) === 2) {
                    $annotations[] = $methodComment[1];
                }
            }
        }
        return $annotations;
    }
}
if (!function_exists('getClassStaticMethodsFromAnnotations')) {
    /**
     * @param class-string|object|trait-string $class
     * 
     * @return list<object{'name': string, 'type': string}>
     */
    function getClassStaticMethodsFromAnnotations(string|object $class): array
    {
        $annotations = [];
        $methodsDocComment = getClassMethodsAllFromDocComment($class);
        if ($methodsDocComment !== null) {
            foreach ($methodsDocComment as $v) {
                $methodComment = explode(" ", $v);
                if (count($methodComment) === 3 && $methodComment[0] === 'static') {
                    $annotations[] = (object) ['name' => $methodComment[2], 'type' => $methodComment[1]];
                }
            }
        }
        return $annotations;
    }
}
if (!function_exists('minifier')) {
    function minifier(string $code): string
    {
        $search = array(
            // Remove whitespaces after tags
            '/\>[^\S]+/s',
            // Remove whitespaces before tags
            '/[^\S]+\</s',
            // Remove multiple whitespace sequences
            '/(\s)+/s',
            // Removes comments
            '/<!--(.|\s)*?-->/'
        );
        $replace = array('>', '<', '\\1');
        $code = preg_replace($search, $replace, $code);
        return $code;
    }
}
