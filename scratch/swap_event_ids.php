<?php
require_once 'main/basic_functions.php';
require_once 'main/report_functions.php';
require_once 'main/label.php';

$bf = new Report_Functions();

echo "Auditing and correcting event identifier inversion...\n";

$events = $bf->getQueryRecords("SELECT * FROM " . $GLOBALS['event_table']);
foreach ($events as $e) {
    $id = $e['id'];
    $eid = $e['event_id'];
    $enum = $e['event_number'];
    
    echo "ID: $id | Current event_id: $eid | Current event_number: $enum\n";
    
    // Check if event_id is plain text (starts with 'EVT') and event_number is encrypted (doesn't start with 'EVT')
    if (strpos($eid, 'EVT') === 0 && strpos($enum, 'EVT') !== 0) {
        echo "Inversion detected for Event ID $id! Swapping values...\n";
        
        $bf->UpdateSQL(
            $GLOBALS['event_table'],
            [
                'event_id' => $enum,
                'event_number' => $eid
            ],
            'id = :id',
            [':id' => $id]
        );
        
        echo "Successfully swapped!\n";
    } else {
        echo "No swap needed for Event ID $id.\n";
    }
}
echo "Done!\n";
?>
