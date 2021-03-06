<?php

require_once __DIR__.'/../Migration.class.php';

class ThWboardMigration4 extends Migration
{
    public function __construct()
    {
        parent::__construct(
            '2.83', '2.84',
            "ThWboard Development Team",
            "This migration requires an unmodified database schema based on version 2.83"
        );
    }

    function writestyle(PDO $pdo, $id)
    {
        $stmt = $pdo->prepare(
<<<SQL
SELECT
    colorbg,
    colorbgfont,
    color4,
    col_he_fo_font,
    color1,
    CellA,
    CellB,
    border_col,
    color_err,
    col_link,
    col_link_v,
    col_link_hover,
    stdfont
FROM
    {$pdo->prefix}style
WHERE
    styleid = :id
SQL
        );

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $style = $style->fetch();

        $str = "a:link { color: $style[col_link]; text-decoration: none; }\n".
          "a:visited { color: $style[col_link_v]; text-decoration: none; }\n".
          "a:hover { color: $style[col_link_hover]; text-decoration: none; }\n".
          "a:active { color: $style[col_link]; text-decoration: none; }\n".
          "a.bglink:link { color: $style[colorbgfont]; text-decoration: underline; }\n".
          "a.bglink:visited { color: $style[colorbgfont]; text-decoration: underline; }\n".
          "a.bglink:hover { color: $style[colorbgfont]; text-decoration: none; }\n".
          "a.bglink:active { color: $style[colorbgfont]; text-decoration: underline; }\n".
          "a.hefo:link { color: $style[col_he_fo_font]; text-decoration: underline; }\n".
          "a.hefo:visited { color: $style[col_he_fo_font]; text-decoration: underline; }\n".
          "a.hefo:hover { color: $style[col_he_fo_font]; text-decoration: none; }\n".
          "a.hefo:active { color: $style[col_he_fo_font]; text-decoration: underline; }\n".
          "body { background-color: $style[colorbg]; color: $style[color1]; }\n".
          ".border-col { background-color: $style[border_col]; }\n".
          ".cella { background-color: $style[CellA]; }\n".
          ".cellb { background-color: $style[CellB]; }\n".
          ".color4 { background-color: $style[color4]; }\n".
          ".tbbutton { font-family: Verdana; font-size: 8pt; }\n".
          ".tbinput { background-color: #EEEEEE; font-family: Verdana; font-size: 8pt; }\n".
          ".tbselect { background-color: #EEEEEE; font-family: Verdana; font-size: 8pt; }\n".
          ".tbtextarea { background-color: #EEEEEE; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10pt; }\n".
          ".smallfont { font-family: $style[font]; font-size:10px; }\n".
          ".stdfont { font-family:$style[font]; font-size:12px; }\n".
          ".header { color: $style[col_he_fo_font]; font-size: 12pt; }";

        $f = fopen("../templates/css/$id.css", "w");

        fwrite($f, $str);

        fclose($f);
    }

    function updateboard(PDO $pdo, $boardid)
    {
        $thread_stmt = $pdo->prepare(
<<<SQL
SELECT
    threadid,
    threadtopic,
    threadtime,
    threadlastreplyby,
    COUNT(threadid) AS threadcount,
    SUM(threadreplies) + COUNT(threadid) AS postcount
FROM
    {$pdo->prefix}thread
WHERE
    threadlink = 0 AND
    boardid = :boardid
GROUP BY
    threadid
ORDER BY
    threadtime DESC
LIMIT
    1
SQL
        );

        $thread_stmt->bindValue(':boardid', $boardid, PDO::PARAM_INT);
        $thread_stmt->execute();

        $board_stmt = $pdo->prepare(
<<<SQL
UPDATE
    {$pdo->prefix}board
SET
    boardlastpost = :lastpost,
    boardthreadid = :threadid,
    boardthreadtopic = :threadtopic,
    boardlastpostby = :lastpostby,
    boardposts = :postcount,
    boardthreads = :threadcount
WHERE
    boardid = :boardid
SQL
        );

        $board_stmt->bindValue(':boardid', $boardid, PDO::PARAM_INT);

        if ($thread_stmt->rowCount()) {
            $board_stmt->bindValue(':lastpost', 0, PDO::PARAM_INT);
            $board_stmt->bindValue(':threadid', 0, PDO::PARAM_INT);
            $board_stmt->bindValue(':threadtopic', "", PDO::PARAM_STR);
            $board_stmt->bindValue(':lastpostby', "", PDO::PARAM_STR);
            $board_stmt->bindValue(':postcount', 0, PDO::PARAM_INT);
            $board_stmt->bindValue(':threadcount', 0, PDO::PARAM_INT);
        } else {
            $thread = $thread_stmt->fetch();

            $board_stmt->bindValue(':lastpost', $thread['threadtime'], PDO::PARAM_INT);
            $board_stmt->bindValue(':threadid', $thread['threadid'], PDO::PARAM_INT);
            $board_stmt->bindValue(':threadtopic', $thread['threadtopic'], PDO::PARAM_STR);
            $board_stmt->bindValue(':lastpostby', $thread['threadlastreplyby'], PDO::PARAM_STR);
            $board_stmt->bindValue(':postcount', $thread['postcount'], PDO::PARAM_INT);
            $board_stmt->bindValue(':threadcount', $thread['threadcount'], PDO::PARAM_INT);
        }

        $board_stmt->execute();
      }

    public function upgrade(PDO $pdo)
    {
        if ($this->fromVersion != schema_version($pdo)) {
            throw new RuntimeException(lng('cantexec'));
        }

        /* no update if templates/css/ does not exist */
        if (!is_writable(__DIR__.'/../../templates/css/') || !is_writable(__DIR__.'/../../templates/css/1.css')) {
            throw new RuntimeException("You must set templates/css/ and any css files inside to be writable by php scripts. Often this means setting permissions to 777.");
        }

        $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}user
DROP COLUMN
    usernoipcheck
SQL
        );

        $pdo->exec(
<<<SQL
UPDATE
    {$pdo->prefix}registry
SET
    keydescription = 'Used in eMails &amp; some board features<br>It is vital to set this correctly! '
WHERE
    keyname = 'board_baseurl'
SQL
        );

        $styles = $pdo->query(
<<<SQL
SELECT
    styleid
FROM
    {$pdo->prefix}style
WHERE
    styleid != 1
SQL
        )->fetchAll(PDO::FETCH_COLUMN, 0);

        foreach ($styles as $styleid) {
            $this->writestyle($pdo, $v);
        }

        $boards = $pdo->query(
<<<SQL
SELECT
    boardid
FROM
    {$pdo->prefix}board
SQL
        )->fetchAll(PDO::FETCH_COLUMN, 0);

        foreach ($boards as $boardid) {
            $this->updateboard($pdo, $boardid);
        }

        $stmt = $pdo->prepare(
<<<SQL
UPDATE
    {$pdo->prefix}registry
SET
    keyvalue = :version
WHERE
    keyname = 'version'
SQL
        );

        $stmt->bindValue(':version', $this->toVersion, PDO::PARAM_STR);
        $stmt->execute();

        return 0;
    }

    public function downgrade(PDO $pdo)
    {
        if (table_exists($pdo, $pdo->prefix.'user')) {
            $pdo->exec(
<<<SQL
ALTER TABLE
    {$pdo->prefix}user
ADD COLUMN
    usernoipcheck TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
SQL
            );
        }

        if (table_exists($pdo, $pdo->prefix.'registry')) {
            $stmt = $pdo->prepare(
<<<SQL
UPDATE
    {$pdo->prefix}registry
SET
    keyvalue = :version
WHERE
    keyname = 'version'
SQL
            );

            $stmt->bindValue(':version', $this->fromVersion, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}

return new ThWboardMigration4();
