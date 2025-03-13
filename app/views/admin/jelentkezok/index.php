<?php
require APPROOT . '/views/includes/head.php';
require APPROOT . '/views/includes/adminNavigation.php';
?>


</head>

<body>

    <div class="main">
        <p style="align-self: flex-start; margin-left: 15%;">Jelentkezők Száma: <?php echo $data["szam"]->jelentkezok_szama ?></p>
        <div>
            <input id="searchBoxAdmin" type="text" class="kereses" placeholder="Keresés..." onkeyup="keresesAdmin()">
        </div>
    </div>

    <span id="keresesiEredmenyek"></span>


    <table class="customers" id="torlesAdmin">
        <tr>
            <th>Látogató neve</th>
            <th>Látogató email címe</th>
            <th>Szülő email címe</th>
            <?php if (count($data["idopontok"]) > 0) : ?>
                <?php foreach ($data["idopontok"] as $sor): ?>

                    <th><?php echo $sor->idopont ?></th>

                <?php endforeach; ?>
            <?php endif; ?>
            <th>Műveletek</th>
        </tr>
        <?php if (count($data["jelentkezok"]) > 0) : ?>
            <?php foreach ($data["jelentkezok"] as $sor): ?>
                <tr>
                    <td><?php echo $sor->jelentkezo ?></td>
                    <td contenteditable="true" id="email" onkeydown="saveEmailChange(event, '<?php echo $sor->jelentkezo_id ?>')"><?php echo $sor->email ?></td>
                    <td contenteditable="true" id="emailSzulo" onkeydown="saveParentEmailChange(event, '<?php echo $sor->jelentkezo_id ?>')"><?php echo $sor->parent_email ?></td>
                    <?php foreach ($data["idopontok"] as $sor2): ?>
                        <td>
                            <?php

                            if (isset($sor->idopont_terem) && !empty($sor->idopont_terem)) {
                                $idopontTeremParts = explode(',', $sor->idopont_terem);
                                $matchFound = false;

                                foreach ($idopontTeremParts as $part) {
                                    $idopontTeremArray = explode(';', $part);

                                    if (count($idopontTeremArray) > 1 && $idopontTeremArray[0] == substr($sor2->idopont, 0, -3)) {
                                        echo htmlspecialchars($idopontTeremArray[1]);
                                        echo "<br>";
                                        $matchFound = true;
                                    }
                                }
                                // if (!$matchFound) {
                                //     echo "";
                                // }
                            }
                            // else {
                            //     echo "";
                            // }
                            ?>
                        </td>
                    <?php endforeach; ?>

                    <td>
                        <a class="jelentkezoTorles" onclick="return confirm('Biztos megjelent?')" href="<?php echo URLROOT ?>/admin/felhasznaloEngedelyezese/<?php echo $sor->email ?>"><i style="color: <?php echo $sor->megjelent ? "green" : "red" ?>;" class='bx bxs-been-here'></i></a>

                        <a class="jelentkezoTorles" onclick="return confirm('Biztos megfelelnek az adatok?')" href="<?php echo URLROOT ?>/admin/felhasznaloMegfelel/<?php echo $sor->email ?>"><i style="color: <?php echo $sor->megfelel ? "green" : "red" ?>;" class='bx bxs-user-check'></i></a>
                        <a class="jelentkezoTorles" onclick="return confirm('Biztos törölni szeretnéd?')" href="<?php echo URLROOT ?>/admin/felhasznaloTorlese/<?php echo $sor->email ?>"><i class='bx bxs-trash'></i></a></h3>
                    </td>

                </tr>

            <?php endforeach; ?>
        <?php endif; ?>


    </table>
    <!--Felhasználók exportálása-->

    <!-- <form class="export" action="<?= URLROOT; ?>/admin/mindenkiexportPDF/" method="post">
        <button type="submit" class="export_gomb export2">Minden felhasználó exportálása</button>
    </form>
    <form class="export" action="<?= URLROOT; ?>/admin/exportPDF/" method="post">
        <button type="submit" class="export_gomb export2">Felhasználók exportálása</button>
    </form> -->

    <!--Összes felhasználó törlése-->
    <form class="export" onclick="return confirm('Biztos törölni szeretnéd az összes felhasználót?')" action="<?= URLROOT; ?>/admin/osszesFelhasznaloTorlese/" method="post">
        <button type="submit" class="export_gomb export2">Felhasználók törlése</button>
    </form>

    <script src="<?php echo URLROOT ?>/public/js/script.js"></script>
    <script>
        function saveEmailChange(event, id) {
            if (event.code === "Enter") {
                event.preventDefault();

                fetch("<?php echo URLROOT ?>/admin/saveEmailChange", {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    method: "POST",
                    body: JSON.stringify({
                        jelentkezoID: id,
                        email: event.target.innerText
                    })
                }).then(async (response) => {
                    console.log(await response.text());
                }).catch(err => {
                    console.log(err);
                });
            }
        }

        function saveParentEmailChange(event, id) {
            if (event.code === "Enter") {
                event.preventDefault();

                fetch("<?php echo URLROOT ?>/admin/saveParentEmailChange", {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    method: "POST",
                    body: JSON.stringify({
                        jelentkezoID: id,
                        email: event.target.innerText
                    })
                }).then(async (response) => {
                    console.log(await response.text());
                }).catch(err => {
                    console.log(err);
                });
            }
        }
    </script>