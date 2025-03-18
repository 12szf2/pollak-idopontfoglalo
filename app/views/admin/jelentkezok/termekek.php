<table class="customers">
    <tr>
        <th>Látogató neve</th>
        <?php if (count($data["idopontok"]) > 0) : ?>
            <?php foreach ($data["idopontok"] as $sor): ?>

                <th><?php echo $sor->idopont ?></th>

            <?php endforeach; ?>
        <?php endif; ?>
        <th>Műveletek</th>
    </tr>
    <?php if (count($data["termekek"]) > 0) : ?>
        <?php foreach ($data["termekek"] as $sor): ?>
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