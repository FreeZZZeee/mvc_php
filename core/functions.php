<?php

defined("ROOTPATH") or exit('Доступ запрещен!');

checkExtensions();
function checkExtensions(): void
{
    $requiredExtensions = [
        'gd',
        'mysqli',
        'pdo_mysql',
        'pdo_sqlite',
        'curl',
        'fileinfo',
        'exif',
        'mbstring'
    ];

    $notLoaded = [];

    foreach ($requiredExtensions as $ext) {
        if (!extension_loaded($ext)) {
            $notLoaded[] = $ext;
        }
    }

    if (!empty($notLoaded)) {
        show("Загрузите следующие расширения в свой файл php.ini: <br>" . implode("<br>", $notLoaded));
        die();
    }
}

function show(mixed $data): void
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function esc($str): string
{
    return htmlspecialchars($str);
}

/*
 * Проверка существует значение в массиве или объекте
 */
function inMultiArray($elem, $array): bool
{
    if (is_array($array) || is_object($array)) {
        if (is_object($array)) {
            $temp_array = get_object_vars($array);
            if (in_array($elem, $temp_array)) {
                return true;
            }
        }
        if (is_array($array) && in_array($elem, $array)) {
            return true;
        }
        foreach ($array as $array_element) {
            if ((is_array($array_element) || is_object($array_element)) && inMultiArray($elem, $array_element)) {
                return true;
                exit;
            }
        }
    }
    return false;
}

/*
 * Генерация ключа
 */
function getRandomStringMax(int $length): string
{
    $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
    $text = "";

    $length = rand(4, $length);

    for ($i = 0; $i < $length; $i++) {
        $random = rand(0, 61);
        $text .= $array[$random];
    }
    return $text;
}

function redirect(string $path): never
{
    header("Location: " . $_ENV['FRONTEND_URL'] . $path);
    die();
}

function getImage(mixed $file = '', string $type = 'post'): string
{
    $file = $file ?? '';
    if (file_exists($file)) {
        return ROOTPATH . '/' . $file;
    }

    if ($type == 'user') {
        return ROOTPATH . 'assets/img/user.jpg';
    } else {
        return ROOTPATH . 'assets/img/no_image.jpg';
    }
}

function getPaginationVars(): array
{
    $vars = [];
    $vars['page'] = $_GET['page'] ?? 1;
    $vars['page'] = (int)$vars['page'];
    $vars['prev_page'] = $vars['page'] <= 1 ? 1 : $vars['page'] - 1;
    $vars['next_page'] = $vars['page'] + 1;

    return $vars;
}

function message(string $msg = null, bool $clear = false): string|bool
{
    $ses = new Model\Session();

    if (!empty($msg)) {
        $ses->set('message', $msg);
    } elseif (!empty($ses->get('message'))) {
        $msg = $ses->get('message');

        if ($clear) {
            $ses->pop('message');
        }
        return $msg;
    }
    return false;
}

function oldChecked(string $key, string $value, string $default = ""): string
{
    if (isset($_POST[$key])) {
        if ($_POST[$key] == $value) {
            return ' checked ';
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == "GET" && $default == $value) {
        return ' checked ';
    }
    return '';
}

function oldValue(string $key, mixed $default = "", string $mode = 'post'): mixed
{
    $POST = ($mode == 'post') ? $_POST : $_GET;
    if (isset($POST[$key])) {
        return $POST[$key];
    }
    return $default;
}

function oldSelect(string $key, mixed $value, mixed $default = '', string $mode = 'post'): string
{
    $POST = ($mode == 'post') ? $_POST : $_GET;
    if (isset($POST[$key])) {
        if ($_POST[$key] == $value) {
            return 'selected';
        }
    } elseif ($default == $value) {
        return 'selected';
    }
    return "";
}

function get_date($date): string
{
    return date("jS M, Y", strtolower($date));
}

function URL($key): string
{
    $URL = $_GET['url'] ?? 'home';
    $URL = explode("/", trim($URL, "/"));

    return match ($key) {
        'page', 0 => $URL[0] ?? null,
        'section', 'slug', 1 => $URL[1] ?? null,
        'action', 2 => $URL[2] ?? null,
        'id', 3 => $URL[3] ?? null,
        default => null,
    };
}

function addRootToImages($contents)
{

    preg_match_all('/<img[^>]+>/', $contents, $matches);
    if (is_array($matches) && count($matches) > 0) {

        foreach ($matches[0] as $match) {

            preg_match('/src="[^"]+/', $match, $matches2);
            if (!str_contains($matches2[0], 'http')) {
                $contents = str_replace($matches2[0], 'src="' . ROOTPATH . '/' . str_replace('src="', "", $matches2[0]), $contents);
            }
        }
    }
    return $contents;
}

function removeImagesFromContent($content, $folder = "uploads/")
{
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
        file_put_contents($folder . "index.php", "Access Denied!");
    }

    //remove images from content
    preg_match_all('/<img[^>]+>/', $content, $matches);
    $new_content = $content;

    if (is_array($matches) && count($matches) > 0) {

        $image_class = new Model\Image();
        foreach ($matches[0] as $match) {

            if (str_contains($match, "http")) {
                //ignore images with links already
                continue;
            }

            // get the src
            preg_match('/src="[^"]+/', $match, $matches2);

            // get the filename
            preg_match('/data-filename="[^\"]+/', $match, $matches3);

            if (str_contains($matches2[0], 'data:')) {

                $parts = explode(",", $matches2[0]);
                $basename = $matches3[0] ?? 'basename.jpg';
                $basename = str_replace('data-filename="', "", $basename);

                $filename = $folder . "img_" . sha1(rand(0, 9999999999)) . $basename;

                $new_content = str_replace($parts[0] . "," . $parts[1], 'src="' . $filename, $new_content);
                file_put_contents($filename, base64_decode($parts[1]));

                //resize image
                $image_class->resize($filename, 1000);
            }
        }
    }
    return $new_content;
}

function deleteImagesFromContent(string $content, string $content_new = ''): void
{

    //delete images from content
    if (empty($content_new)) {

        preg_match_all('/<img[^>]+>/', $content, $matches);

        if (is_array($matches) && count($matches) > 0) {
            foreach ($matches[0] as $match) {

                preg_match('/src="[^"]+/', $match, $matches2);
                $matches2[0] = str_replace('src="', "", $matches2[0]);

                if (file_exists($matches2[0])) {
                    unlink($matches2[0]);
                }

            }
        }
    } else {

        //compare old to new and delete from old what inst in the new
        preg_match_all('/<img[^>]+>/', $content, $matches);
        preg_match_all('/<img[^>]+>/', $content_new, $matches_new);

        $old_images = [];
        $new_images = [];

        /** collect old images **/
        if (is_array($matches) && count($matches) > 0) {
            foreach ($matches[0] as $match) {

                preg_match('/src="[^"]+/', $match, $matches2);
                $matches2[0] = str_replace('src="', "", $matches2[0]);

                if (file_exists($matches2[0])) {
                    $old_images[] = $matches2[0];
                }

            }
        }

        /** collect new images **/
        if (is_array($matches_new) && count($matches_new) > 0) {
            foreach ($matches_new[0] as $match) {

                preg_match('/src="[^"]+/', $match, $matches2);
                $matches2[0] = str_replace('src="', "", $matches2[0]);

                if (file_exists($matches2[0])) {
                    $new_images[] = $matches2[0];
                }

            }
        }

        /** compare and delete all that dont appear in the new array **/
        foreach ($old_images as $img) {

            if (!in_array($img, $new_images)) {

                if (file_exists($img)) {
                    unlink($img);
                }
            }
        }
    }

}
