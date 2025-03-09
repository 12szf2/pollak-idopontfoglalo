<?php

class MainModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Főoldal adatainak lekérdezése
    public function kartyaLekerdezes()
    {
        $this->db->query(
            'SELECT
                e.tema, e.cim, e.leiras, e.kep, e.datum, e.id AS "esemeny_id", s.neve,
                u.nev, t.neve, t.ferohely, count(j.email) as "jelentkezok"
            FROM
                esemenyek e
            INNER JOIN
                users u ON e.tanarID = u.id
            INNER JOIN
                tanterem t ON e.tanteremID = t.id
            INNER JOIN
                szakok s ON e.szakID = s.id
            LEFT JOIN
                jelentkezok_vt j ON e.id = j.esemenyID AND j.torolt = false AND j.visszaigazolt = true
            WHERE
                e.torolt = false
            GROUP BY
                e.cim, e.leiras, e.kep, e.datum, e.id, u.nev, t.neve, t.ferohely, s.neve
            '
        );

        $results = $this->db->resultSet();

        return $results;
    }

    // Időpontok lekérdezése
    public function idopontokLekerdezesNap()
    {
        $this->db->query('SELECT distinct DATE(datum) AS datum FROM esemenyek WHERE torolt = false');
        $results = $this->db->resultSet();

        return $results;
    }

    public function idopontokLekerdezesOra()
    {
        $this->db->query('SELECT distinct TO_CHAR(datum, \'HH24:MI:SS\') AS datum FROM esemenyek WHERE torolt = false');
        $results = $this->db->resultSet();

        return $results;
    }

    // Szakok lekérdezése
    public function szakokLekerdezes()
    {
        $this->db->query('SELECT id, neve FROM szakok');
        $results = $this->db->resultSet();

        return $results;
    }

    // Oktatók lekérdezése
    public function oktatokLekerdezes()
    {
        $this->db->query('SELECT DISTINCT users.nev, users.id FROM users INNER JOIN esemenyek ON esemenyek.tanarID = users.id AND esemenyek.torolt = false;');
        $results = $this->db->resultSet();

        return $results;
    }

    // Tantermek lekérdezése
    public function teremLekerdezes()
    {
        $this->db->query('SELECT DISTINCT tanterem.neve, tanterem.id FROM tanterem INNER JOIN esemenyek ON esemenyek.tanteremID = tanterem.id AND tanterem.torolt = false;');
        $results = $this->db->resultSet();

        return $results;
    }

    /* Ékezetek helyett '_' jel */
    private function replaceHungarianAccents($string)
    {
        // A HTML entitások visszacserélése ékezetekre
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');

        // A magyar ékezetek cseréje '_'-re, hogy az ékezetmentes keresés is működjön
        $accents = ['á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ö', 'Ö', 'ő', 'Ő', 'ú', 'Ú', 'ü', 'Ü', 'ű', 'Ű'];
        $replacement = '_';

        return str_replace($accents, $replacement, $string);
    }

    /* Az adott szórészletet tartalmazó termékek lekérdezése */
    public function termekekKeresese($keresendo)
    {
        $keresendo = $this->replaceHungarianAccents($keresendo);

        $this->db->query(
            "SELECT
           e.tema, e.cim, e.leiras, e.kep, e.datum, e.id AS 'esemeny_id', s.neve,
            u.nev, t.neve, t.ferohely, count(j.email) as 'jelentkezok'
        FROM
            esemenyek e
        INNER JOIN
            users u ON e.tanarID = u.id
        INNER JOIN
            tanterem t ON e.tanteremID = t.id
        LEFT JOIN
            jelentkezok j ON e.id = j.esemenyID AND j.torolt = false
        INNER JOIN
            szakok s ON e.szakID = s.id
        WHERE
            e.torolt = false
            AND (e.cim LIKE :keresendo OR e.leiras LIKE :keresendo OR u.nev LIKE :keresendo)
        GROUP BY
            e.cim, e.leiras, e.kep, e.datum, e.id, u.nev, t.neve, t.ferohely;     
        "
        );

        $this->db->bind(':keresendo', "%$keresendo%");
        return $this->db->resultSet();
    }

    // Termékek szűrése
    public function termekekSzurese($szuroObj)
    {
        $this->db->query(
            "SELECT
                   e.tema, e.cim, e.leiras, e.kep, e.datum, e.id AS esemeny_id, s.neve,
                    u.nev, t.neve, t.ferohely, count(j.email) as jelentkezok
                FROM
                    esemenyek e
                INNER JOIN
                    users u ON e.tanarID = u.id
                INNER JOIN
                    tanterem t ON e.tanteremID = t.id
                INNER JOIN
                    szakok s ON e.szakID = s.id
                LEFT JOIN
                    jelentkezok j ON e.id = j.esemenyID AND j.torolt = false
                WHERE
                    e.torolt = false
                    AND (:szak::text = '' OR s.id = :szak::int)
                    AND (:tanar::text = '' OR u.id = :tanar::int)
                    AND (:terem::text = '' OR t.id = :terem::int)
                    AND (:nap::date IS NULL OR DATE(e.datum) = :nap::date)
                    AND (:ora::text = '' OR TO_CHAR(e.datum, 'HH24:MI') >= :ora)
                GROUP BY
                    e.cim, e.leiras, e.kep, e.datum, e.id, u.nev, t.neve, t.ferohely, s.neve;
            "
        );

        $this->db->bind(':nap', empty($szuroObj['nap']) ? null : $szuroObj['nap']);
        $this->db->bind(':ora', $szuroObj['ora']);
        $this->db->bind(':tanar', $szuroObj['oktatok']);
        $this->db->bind(':szak', $szuroObj['szak']);
        $this->db->bind(':terem', $szuroObj['termek']);
        return $this->db->resultSet();
    }
}
