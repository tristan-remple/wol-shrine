<?php

session_start();
include("../data/db.php");

$id = intval($_GET["id"]);

$q = mysqli_query($db, "SELECT * FROM `wols` WHERE `id` = '$id'");
if (($q !== false) && (mysqli_num_rows($q) > 0)) {
    $data = mysqli_fetch_array($q);
} else {
    header("Location: error.php?err=url");
}

$nick = preg_replace('/[\'|-]?/', '', strtolower($data["first_name"]));

$creator_id = $data['creator'];
$own = mysqli_query($db, "SELECT * FROM `creators` WHERE `id` = '$creator_id'");
$creator_data = mysqli_fetch_array($own);

$job_1 = $data['job_1'];
$j1 = mysqli_query($db, "SELECT `title` FROM `jobs` WHERE `id` = '$job_1'");
$j1_data = mysqli_fetch_array($j1);
$j1_title = $j1_data['title'];
$j1_img = preg_replace('/[ ]?/', '', $j1_title).'.png';

if ($data['job_2'] !== NULL) {
    $job_2 = $data['job_2'];
    $j2 = mysqli_query($db, "SELECT `title` FROM `jobs` WHERE `id` = '$job_2'");
    $j2_data = mysqli_fetch_array($j2);
    $j2_title = $j2_data['title'];
    $j2_img = preg_replace('/[ ]?/', '', $j2_title).'.png';
} else {
    $j2_title = NULL;
    $j2_img = NULL;
}

if ($data['job_3'] !== NULL) {
    $job_3 = $data['job_3'];
    $j3 = mysqli_query($db, "SELECT `title` FROM `jobs` WHERE `id` = '$job_3'");
    $j3_data = mysqli_fetch_array($j3);
    $j3_title = $j3_data['title'];
    $j3_img = preg_replace('/[ ]?/', '', $j3_title).'.png';
} else {
    $j3_title = NULL;
    $j3_img = NULL;
}

$postq = mysqli_query($db, "SELECT `wol_text`.`date` AS `date`, `wol_text`.`blurb` AS `description`,
`wols_t1`.`first_name` AS `chara_1`, `wols_t2`.`first_name` AS `chara_2`, `wols_t3`.`first_name` AS `chara_3`,
`wol_text`.`chara_1` AS `id_1`, `wol_text`.`chara_2` AS `id_2`, `wol_text`.`chara_3` AS `id_3`,
 `wol_text`.`type` AS `type`, NULL AS `artist`, NULL AS `alt`,
`wol_text`.`title` AS `title`, `wol_text`.`warnings` AS `warnings`, `wol_text`.`filename` AS `filename`
FROM `wol_text`
LEFT OUTER JOIN `wol_pics` ON `wol_pics`.`date` = `wol_text`.`date`
LEFT JOIN `wols` `wols_t1` ON `wols_t1`.`id` = `wol_text`.`chara_1`
LEFT JOIN `wols` `wols_t2` ON `wols_t2`.`id` = `wol_text`.`chara_2`
LEFT JOIN `wols` `wols_t3` ON `wols_t3`.`id` = `wol_text`.`chara_3`
WHERE `wol_text`.`chara_1` = $id OR `wol_text`.`chara_2` = $id  OR `wol_text`.`chara_3` = $id
UNION
SELECT `wol_pics`.`date` AS `date`, `wol_pics`.`comment` AS `description`,
`wol_pics`.`chara_1` AS `chara_1`, `wol_pics`.`chara_2` AS `chara_2`, `wol_pics`.`chara_3` AS `chara_3`,
`wols_p1`.`id` AS `id_1`, `wols_p2`.`id` AS `id_2`, `wols_p3`.`id` AS `id_3`,
 `wol_pics`.`img_type` AS `type`, `wol_pics`.`artist` AS `artist`, `wol_pics`.`alt` AS `alt`,
`wol_text`.`title` AS `title`, `wol_text`.`warnings` AS `warnings`, `wol_pics`.`filename` AS `filename`
FROM `wol_pics`
LEFT OUTER JOIN `wol_text` ON `wol_pics`.`date` = `wol_text`.`date`
LEFT JOIN `wols` `wols_p1` ON `wols_p1`.`first_name` = `wol_pics`.`chara_1`
LEFT JOIN `wols` `wols_p2` ON `wols_p2`.`first_name` = `wol_pics`.`chara_2`
LEFT JOIN `wols` `wols_p3` ON `wols_p3`.`first_name` = `wol_pics`.`chara_3`
WHERE `wols_p1`.`id` = $id OR `wols_p2`.`id` = $id OR `wols_p3`.`id` = $id
UNION
SELECT `wol_ships`.`date` AS `date`, `wol_ships`.`ship_text` AS `description`,
`wol_ships`.`chara_1` AS `chara_1`, `wol_ships`.`chara_2` AS `chara_2`, NULL as `chara_3`,
`wols_s1`.`id` AS `id_1`, `wols_s2`.`id` AS `id_2`, NULL AS `id_3`,
'ship' AS `type`, NULL AS `artist`, NULL AS `alt`,
NULL AS `title`, NULL AS `warnings`, NULL AS `filename`
FROM `wol_ships`
LEFT OUTER JOIN `wol_text` ON `wol_ships`.`date` = `wol_text`.`date`
LEFT JOIN `wols` `wols_s1` ON `wols_s1`.`first_name` = `wol_ships`.`chara_1`
LEFT JOIN `wols` `wols_s2` ON `wols_s2`.`first_name` = `wol_ships`.`chara_2`
WHERE `wols_s1`.`id` = $id OR `wols_s2`.`id` = $id
UNION
SELECT `retainers`.`date` AS `date`, `retainers`.`blurb` AS `description`,
`wols_r1`.`first_name` AS `chara_1`, NULL as `chara_2`, NULL as `chara_3`,
`retainers`.`employer` AS `id_1`, `retainers`.`id` AS `id_2`, NULL AS `id_3`,
'retainer' AS `type`, NULL AS `artist`, NULL AS `alt`,
NULL AS `title`, NULL AS `warnings`, NULL AS `filename`
FROM `retainers`
LEFT OUTER JOIN `wol_text` ON `retainers`.`date` = `wol_text`.`date`
LEFT JOIN `wols` `wols_r1` ON `wols_r1`.`id` = `retainers`.`employer`
WHERE `wols_r1`.`id` = $id
ORDER BY `date` DESC;");

echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amarante&family=Glory:wght@300;600&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="css/', $nick, '.css" id="colorstyle">
    <link rel="stylesheet" type="text/css" href="css/layout.css">

    <script src="scripts/lightbox.js" defer></script>
    <script src="scripts/readmore.js" defer></script>

    <title>', $data['first_name'], ' ', $data['last_name'], '</title>
</head>
<body>
    <div id="lightbox" class="hidden">
        <img id="lit-img" src="img/caps/ffxiv_08222022_193059_298.png">
        <div id="close" class="box bordered" tabindex="0"><h3>X</h3></div>
    </div>
    <main id="main" class="chara-main" tabindex="0">
    
        <div class="text-col fixed">
            <div class="portrait bordered-img">
                <img id="inner-portrait" class="bordered-img" src="img/', $nick, '-profile.png" tabindex="0" alt="A bust illustration of ', $data['first_name'], '">
            </div>
            <div id="sidebar">
                <div id="stats" class="box bordered">
                    <div class="title-card shape box bordered"><h3>', $data['first_name'], ' ', $data['last_name'], '</h3></div>
                    <p>
                        <b>Pronouns:</b> ', $data['pronouns'], '<br>
                        <b>Race:</b>', $data['race'], ' - ', $data['clan'], '<br>
                        <b>Height:</b> ', $data['height'], '<br>
                        <b>Color:</b>', $data['color'], '<br>
                        <b>Chocobo Name:</b> ', $data['chocobo_name'], '<br>
                        <b>Owner:</b> <a href="', $creator_data['url'], '">', $creator_data['name'], '</a>
                    </p>
                </div>
                <div id="jobs" class="box bordered">
                    <img class="job" src="img/icons/', $j1_img, '" alt="', $j1_title, '" title="', $j1_title, '">';
                    if ($j2_title !== NULL) {
                        echo '<img class="job" src="img/icons/', $j2_img, '" alt="', $j2_title, '" title="', $j2_title, '">';
                    }
                    if ($j3_title !== NULL) {
                        echo '<img class="job" src="img/icons/', $j3_img, '" alt="', $j3_title, '" title="', $j3_title, '">';
                    }
                    echo '<a id="lodestone" class="shape box bordered" href="https://na.finalfantasyxiv.com/lodestone/character/', $data['lodestone'], '/">
                        <h3>Lodestone</h3>
                    </a>
                </div>
            </div>
            <a class="shape box bordered" href="index">
                <h3>Return</h3>
            </a>
        </div>

        <div class="text-col"><div id="post-box">
            <div id="tag-box" class="box bordered">
                <div class="title-card shape box bordered"><h3>Tags</h3></div>
                <a href="character?id=', $id, '&post=bio"><div class="tag">#bio</div></a>
                <a href="character?id=', $id, '&post=art"><div class="tag">#art</div></a>
                <a href="character?id=', $id, '&post=screencaps"><div class="tag">#screencaps</div></a>
                <a href="character?id=', $id, '&post=prose"><div class="tag">#prose</div></a>
                <a href="character?id=', $id, '&post=shiptext"><div class="tag">#shiptext</div></a>
                <a href="character?id=', $id, '&post=retainers"><div class="tag">#retainers</div></a>
            </div>';

            $post_number = 1;
            while ($row = mysqli_fetch_array($postq)) {

                $date = date_create($row['date']);
                $visDate = date_format($date, "F j, Y");

                if ($row['type'] === 'bio') {
                    echo '<div class="post box bordered bio">
                        <h3>Bio</h3>
                        <p>
                        <i>', $visDate, '</i><br><br>',
                            $row['description'],
                        '</p>
                        <div class="post-footer shape box bordered">
                            <a href="character?id=', $id, '&post=bio"><div class="tag">#bio</div></a>
                        </div>
                    </div>';
                } elseif ($row['type'] === 'prose') {

                    echo '<div class="post box bordered prose">
                        <h3>', $row['title'], '</h3>
                        <div class="rowbox prose-row">
                            <a class="prose-link" href="character.php?id=', $row['id_1'], '">
                                <img class="prose-pic" src="img/', preg_replace('/[\'|-]?/', '', $row['chara_1']), '-icon.png">
                            </a>';
                            if ($row['chara_2'] !== NULL) {
                                echo '<a class="prose-link" href="character.php?id=', $row['id_2'], '">
                                    <img class="prose-pic" src="img/', preg_replace('/[\'|-]?/', '', $row['chara_2']), '-icon.png">
                                </a>';
                            }
                            if ($row['chara_3'] !== NULL) {
                                echo '<a class="prose-link" href="character.php?id=', $row['id_3'], '">
                                    <img class="prose-pic" src="img/', preg_replace('/[\'|-]?/', '', $row['chara_3']), '-icon.png">
                                </a>';
                            }
                        echo '</div>
                        <p>
                        <i>', $visDate, '</i><br><br>
                            <i>Contains: ', $row['warnings'], '</i><br><br>',
                            $row['description'],
                        '</p>
                        <h3 id="toggle-', $post_number, '" class="read-more tag" tabindex="0">Read More...</h3>
                        <p class="hidden" id="', $post_number, '">';

                        $filepath = 'text/'.$row['filename'].'.txt';
            
                        $text = fopen($filepath, "r") or die("The specified text could not be found.");
                        while (!feof($text)) {
                            echo fgets($text) . "<br>";
                        }
                        fclose($text);

                        echo '</p>
                        <div class="post-footer shape box bordered">
                            <a href="character?id=', $id, '&post=prose"><div class="tag">#prose</div></a>
                        </div>
                    </div>';
                } elseif ($row['type'] === 'screencap') {
                    echo '<div class="post box bordered screencap">
                        <img class="posted-image" src="img/caps/', $row['filename'], '.png" tabindex="0" alt="', $row['alt'], '">
                        <p>
                        <i>', $visDate, '</i><br><br>',
                            $row['description'],
                        '</p>
                        <div class="post-footer shape box bordered">
                            <a href="character?id=', $id, '&post=screencaps"><div class="tag">#screencaps</div></a>
                        </div>
                    </div>';
                } elseif ($row['type'] === 'art') {
                    echo '<div class="post box bordered art">
                        <img class="posted-image" src="img/art/', $row['filename'], '.png" tabindex="0" alt="', $row['alt'], '">
                        <p>
                        <i>', $row['artist'], ' - ', $visDate, '</i><br><br>',
                            $row['description'],
                        '</p>
                        <div class="post-footer shape box bordered">
                            <a href="character?id=', $id, '&post=art"><div class="tag">#art</div></a>
                        </div>
                    </div>';
                } elseif ($row['type'] === 'retainer') {

                    $rid = $row['id_2'];
                    $rq = mysqli_query($db, "SELECT * FROM `retainers` WHERE `id` = $rid");
                    $r_info = mysqli_fetch_array($rq);

                    echo '<div class="post box bordered retainer">
                        <img class="retainer-pic" src="img/r2 - ', $r_info['name'], '.png" alt="A round screenshot icon of ', $r_info['name'], '.">
                        <h3>', $r_info['name'], '</h3>
                        <p class="ship-text">
                            <i>', $r_info['nature'], ' ', $r_info['clan'], ' ', $r_info['race'], ' - ', $r_info['pronouns'],
                            '</i><br><br>',
                            $r_info['blurb'],
                        '</p>
                        <div class="post-footer shape box bordered">
                            <a href="character?id=', $id, '&post=retainers"><div class="tag">#retainers</div></a>
                        </div>
                    </div>';
                } elseif ($row['type'] === 'ship') {

                    if ($row['id_1'] != $id) {
                        $ship_name = $row['chara_1'];
                        $ship_id = $row['id_1'];
                    } else {
                        $ship_name = $row['chara_2'];
                        $ship_id = $row['id_2'];
                    }

                    echo '<div class="post box bordered ship">
                        <a class="ship-link" href="character.php?id=', $ship_id, '">
                            <img class="ship-pic" src="img/', preg_replace('/[\'|-]?/', '', $ship_name), '-icon.png" alt="A round screenshot icon of ', $ship_name, '.">
                        </a>
                        <h3>', ucfirst($ship_name), '</h3>
                        <p class="ship-text">',
                            $row['description'],
                        '</p>
                        <div class="post-footer shape box bordered">
                            <a href="character?id=', $id, '&post=shiptext"><div class="tag">#shiptext</div></a>
                        </div>
                    </div>';
                }
                $post_number++;
            }
            
        echo '</div></div>
        <div class="img-col">
            <img id="side-img" src="img/', $nick, '-glow.png" alt="A transparent glowing screencap of ', $data['first_name'], '.">
        </div>

    </main>
</body>
</html>';
?>