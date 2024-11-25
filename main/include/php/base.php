<? function checkAllDatabases($optimizeTable, $repairTable)
{
    $tableCount = 0;
    $tableErr = 0;
    $ss1 = sql_call("SHOW DATABASES LIKE 'edomi%'");
    while ($db = sql_result($ss1)) {
        $nameDb = $db[key($db)];
        $ss2 = sql_call("SHOW TABLE STATUS IN " . $nameDb);
        while ($table = sql_result($ss2)) {
            if (strtoupper($table['Engine']) != 'MEMORY') {
                $ss3 = sql_call("CHECK TABLE " . $nameDb . "." . $table['Name']);
                if ($status = sql_result($ss3)) {
                    $tableCount++;
                } else {
                    $status['Msg_text'] = 'Table not found';
                    $tableErr++;
                }
                sql_close($ss3);
                if (strtoupper($status['Msg_text']) != 'OK') {
                    if ($repairTable) {
                        writeToLog(1, false, 'Datenbank: ' . $nameDb . '.' . $table['Name'] . ' (automatische Reparatur aktiviert - siehe System-Log): ' . $status['Msg_text'], 'ERROR');
                    } else {
                        writeToLog(1, false, 'Datenbank: ' . $nameDb . '.' . $table['Name'] . ' fehlerhaft (' . $status['Msg_text'] . ')', 'ERROR');
                    }
                    if ($repairTable) {
                        sql_call("REPAIR TABLE " . $nameDb . "." . $table['Name']);
                        writeToLog(1, true, 'Datenbank: ' . $nameDb . '.' . $table['Name'] . ' repariert. Die Reparatur kann u.U. wirkungslos bleiben - bitte überprüfen!');
                    }
                    $tableErr++;
                }
                if ($optimizeTable) {
                    sql_call("OPTIMIZE TABLE " . $nameDb . "." . $table['Name']);
                }
            }
        }
        sql_close($ss2);
    }
    sql_close($ss1);
    return array($tableCount, $tableErr);
} ?>
