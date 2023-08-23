<?php

namespace Model;

defined("ROOTPATH") or exit('Доступ запрещен!');

class Pager
{
    public array $links = array();
    public int $offset = 0;
    public int $pageNumber = 1;
    public int $start = 1;
    public int $end = 1;
    public int $limit = 10;
    public string $navClass = "";
    public string $navStyles = "";
    public string $ulClass = "pagination justify-content-center";
    public string $ulStyles = "";
    public string $liClass = "page-item";
    public string $liStyles = "";
    public string $aClass = "page-link";
    public string $aStyles = "";

    public function __construct($limit = 10, $extras = 1)
    {
        // code...
        $page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_number = max($page_number, 1);

        $this->end = $page_number + $extras;
        $this->start = $page_number - $extras;
        if ($this->start < 1) {
            $this->start = 1;
        }

        $this->offset = ($page_number - 1) * $limit;
        $this->pageNumber = $page_number;
        $this->limit = $limit;

        $url = $_GET['url'] ?? '';

        $current_link = $_ENV['FRONTEND_URL'] . "/" . $url . '?' . trim(str_replace("url=", "", str_replace($url, "", $_SERVER['QUERY_STRING'])), '&');
        $current_link = !str_contains($current_link, "page=") ? $current_link . "&page=1" : $current_link;

        if (!str_contains($current_link, "?")) {
            $current_link = str_replace("&page=", "?page=", $current_link);
        }

        $first_link = preg_replace('/page=[0-9]+/', "page=1", $current_link);
        $next_link = preg_replace('/page=[0-9]+/', "page=" . ($page_number + $extras), $current_link);

        $this->links['first'] = $first_link;
        $this->links['current'] = $current_link;
        $this->links['next'] = $next_link;
    }

    public function display($record_count = null): void
    {
        if ($record_count == null)
            $record_count = $this->limit;

        if ($record_count == $this->limit || $this->pageNumber > 1) {
            ?>
            <br class="clearfix">
            <div>
                <nav class="<?= $this->navClass ?>" style="<?= $this->navStyles ?>">
                    <ul class="<?= $this->ulClass ?>" style="<?= $this->ulStyles ?>">
                        <li class="<?= $this->liClass ?>" style="<?= $this->liStyles ?>">
                            <a class="<?= $this->aClass ?>" style="<?= $this->aStyles ?>"
                               href="<?= $this->links['first'] ?>">Первая</a>
                        </li>

                        <?php for ($x = $this->start; $x <= $this->end; $x++): ?>
                            <li class="<?= $this->liClass ?> <?= ($x == $this->pageNumber) ? ' active ' : ''; ?>"
                                style="<?= $this->liStyles ?>">
                                <a class="<?= $this->aClass ?>" style="<?= $this->aStyles ?>" href="
 			    		<?= preg_replace('/page=[0-9]+/', "page=" . $x, $this->links['current']) ?>
 			    		"><?= $x ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="<?= $this->liClass ?>" style="<?= $this->liStyles ?>">
                            <a class="<?= $this->aClass ?>" style="<?= $this->aStyles ?>"
                               href="<?= $this->links['next'] ?>">Следующая</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php
        }
    }
}
