<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 06.08.20
 * Time: 13:09
 */
$this->title = 'Отчет';
?>
<div>
    <table style="margin-left: auto; margin-right: auto;" border="0" width="100%">
        <tbody>
        <tr>
            <td>Дата подачи заявки:</td>
            <td><?=$rdate?></td>
        </tr>
        <tr>
            <td>Адрес эл. почты пользователя:</td>
            <td><?=$email?></td>
        </tr>
        <tr>
            <td>Проверка по базе паспортов:</td>
            <td><?php
                if (null !== $pvalidate){
                    if ($pvalidate){
                        echo 'Действителен';
                    }else{
                        echo 'Не действителен';
                    }
                }else{
                    echo 'Не проводилась';
                }
                ?></td>
        </tr>
        </tbody>
    </table>
    <p>&nbsp;</p>
    <p>Выписка из реестра ЕГРЮЛ:</p>
    <?php
        if (null !== $egrul){
            $egrul = json_decode($egrul);
            $egrul = $egrul->rows[0];
    ?>
    <table style="margin-left: auto; margin-right: auto;" border="0" width="100%">
        <tbody>
        <tr>
            <td>Название:</td>
            <td><?=$egrul->n?></td>
        </tr>
        <tr>
            <td>Дата прекращения деятельности:</td>
            <td><?=$egrul->e?></td>
        </tr>
        <tr>
            <td>ОГРНИП:</td>
            <td><?=$egrul->o?></td>
        </tr>
        <tr>
            <td>ИНН:</td>
            <td><?=$egrul->i?></td>
        </tr>
        <tr>
            <td>Дата присвоения ОГРНИП:</td>
            <td><?=$egrul->r?></td>
        </tr>
        </tbody>
    </table>
    <?php } else {
            echo '<p style="text-align: center">Проверка не проводилась.</p>';
        }?>
    <p>Проверка водительского удостоверения по базе ГИБДД:</p>
    <?php
    if (null !== $gibdd){
        $gibdd = json_decode($gibdd);
        ?>
        <table style="margin-left: auto; margin-right: auto;" border="0" width="100%">
            <tbody>
            <tr>
                <td>Номер\Серия:</td>
                <td><?=$gibdd->doc->num?></td>
            </tr>
            <tr>
                <td>Дата выдачи:</td>
                <td><?=$gibdd->doc->date?></td>
            </tr>
            <tr>
                <td>Категории:</td>
                <td><?=$gibdd->doc->cat?></td>
            </tr>
            <tr>
                <td>День рождения:</td>
                <td><?=$gibdd->doc->bdate?></td>
            </tr>
            <tr>
                <td>Дата окончания:</td>
                <td><?=$gibdd->doc->srok?></td>
            </tr>
            <?php
                if (strlen($gibdd->doc->wanted)>0) {
            ?>
            <tr>
                <td>Документ не действителен и разыскивается с:</td>
                <td><?=$gibdd->doc->wanted?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else {
        echo '<p style="text-align: center">Проверка не проводилась.</p>';
    }?>

    <p>Проверка по базе ФССП:</p>
    <?php
    if (null !== $fssp && sizeof($fssp)>0){
        ?>
    <table style="margin-left: auto; margin-right: auto; padding: 0;" width="100%" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <th style="border: solid 1px lightgray">Должник</th>
            <th style="border: solid 1px lightgray">Номер исполнительного док.</th>
            <th style="border: solid 1px lightgray">Реквизиты</th>
            <th style="border: solid 1px lightgray">Дата окончания</th>
            <th style="border: solid 1px lightgray">Предмет исполнения</th>
            <th style="border: solid 1px lightgray">Отдел ФССП</th>
            <th style="border: solid 1px lightgray">Представитель ФССП</th>
        </tr>
        <?php
        $total_summ = 0;
        foreach ($fssp as $fssp_item){
            $total_summ += $fssp_item['psumm'];
        ?>
            <tr>
                <td style="border: solid 1px lightgray"><?=$fssp_item['owner']?></td>
                <td style="border: solid 1px lightgray"><?=$fssp_item['doc_num']?></td>
                <td style="border: solid 1px lightgray"><?=$fssp_item['doc_id']?></td>
                <td style="border: solid 1px lightgray"><?=$fssp_item['doc_edate']?></td>
                <td style="border: solid 1px lightgray"><?=$fssp_item['summ']?></td>
                <td style="border: solid 1px lightgray"><?=$fssp_item['fssp_div']?></td>
                <td style="border: solid 1px lightgray"><?=$fssp_item['fssp_ex']?></td>
            </tr>
          <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td style="border: solid 1px lightgray" colspan="4"></td>
                    <td style="border: solid 1px lightgray">Итого: <?=$total_summ?></td>
                    <td style="border: solid 1px lightgray" colspan="2"></td>
                </tr>
            </tfoot>
        </table>

    <?php } else {
        echo '<p style="text-align: center">Результатов не найдено</p>';
    }?>

</div>
