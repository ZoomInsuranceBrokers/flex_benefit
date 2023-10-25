<?php

$_YES = 1;
$_NO = 2;
$_RLTN_SELF         = 1;
$_RLTN_FATHER       = 2;
$_RLTN_MOTHER       = 3;
$_RLTN_BRTOHER      = 4;
$_RLTN_SISTER       = 5;
$_RLTN_SPOUSE       = 6;
$_RLTN_FATHERINLAW  = 7;
$_RLTN_MOTHERINLAW  = 8;
$_RLTN_SON          = 9;
$_RLTN_DAUGHTER     = 10;
$_RLTN_LIVEINPARTNER = 11;
$_RLTN_OTHERS       = 12;

$_APPR_STATUS_APPROVED      = 1;
$_APPR_STATUS_REJECTED      = 2;
$_APPR_STATUS_INPROGRESS    = 3;
$_APPR_STATUS_NOT_APPROVED  = 4;


$selectLabel = [-1 => '--Select--'];

/****************** Relationship Types ******************/
$relationship_types = [ 
    $_RLTN_SELF => 'Self',
    $_RLTN_FATHER => 'Father',
    $_RLTN_MOTHER => 'Mother',
    $_RLTN_BRTOHER => 'Brother',
    $_RLTN_SISTER => 'Sister',
    $_RLTN_SPOUSE => 'Spouse',
    $_RLTN_FATHERINLAW => 'Father-in-Law',
    $_RLTN_MOTHERINLAW=> 'Mother-in-Law',
    $_RLTN_SON => 'SON',
    $_RLTN_DAUGHTER => 'Daughter',
    $_RLTN_LIVEINPARTNER => 'Live-In Partner',
    $_RLTN_OTHERS => 'Others'
];
$relationship_typesLE = [ 
    $_RLTN_SPOUSE => 'Spouse',
    $_RLTN_SON => 'Son',
    $_RLTN_DAUGHTER => 'Daughter',
];

function generateJtableOptions($arr1,$arr2) {
    foreach (($arr1 + $arr2) as $k => $v) {
        $generatedOptions[$k] = "{Value:'" . $k . "',DisplayText:'" . $v . "'}";
    }
    return $generatedOptions;
}
/******* GENDER *******/
$gender = [1 => 'Male',2 => 'Female', 3 => 'Others'];
/******* GENDER *******/

/******* BOOLEAN *******/
$boolean = [$_YES => 'Yes',$_NO => 'No'];
/******* BOOLEAN *******/

/******* APPROVAL STATUS *******/
$approval_status = [
    $_APPR_STATUS_APPROVED => 'Approved',
    $_APPR_STATUS_NOT_APPROVED => 'Not Approved',
    $_APPR_STATUS_INPROGRESS =>'In-Progress',
    $_APPR_STATUS_REJECTED => 'Rejected'];
/******* APPROVAL STATUS *******/

/******* Dependent Code *******/
$dependent_code = [ // these values are indexes/keys of relationship_types
    'E' => [1],
    'S' => [6],
    'P' => [2,3],
    'PIL' => [7,8],
    'C' => [9,10],
    'L' => [11],
    'O' => [12, 4,5]
    
];
/******* Dependent Code *******/

/******* Dependent Code UI *******/
$dependent_code_ui = [ // these values are indexes/keys of relationship_types
    'E' => 'Employee',
    'S' => 'Spouse',
    'PIL' => 'Parent-In-Law',
    'P' => 'Parents',
    'C' => 'Children',
    'L' => 'Live-In Partner',
    'O' => 'Others',
    '/' => 'OR'
    
];
/******* Dependent Code *******/

return [
    // relationship array
    'relationship_type' => $relationship_types,
    'dependent_code_ui' => $dependent_code_ui,
    'relationship_type_jTable' => implode(',', generateJtableOptions($selectLabel,$relationship_types)),
    'gender_jTable' => implode(',', generateJtableOptions($selectLabel,$gender)),
    'boolean_jTable' => implode(',', generateJtableOptions($selectLabel,$boolean)),
    'approval_status_jTable' => implode(',', generateJtableOptions($selectLabel,$approval_status)),
    'dependent_code' => $dependent_code,
    'relationshipLE_type_jTable' => generateJtableOptions($selectLabel,$relationship_typesLE),
    
    '$_YES' => $_YES,
    '$_NO' => $_NO,

    '$_RLTN_SELF'         => $_RLTN_SELF         ,         
    '$_RLTN_FATHER'       => $_RLTN_FATHER       ,       
    '$_RLTN_MOTHER'       => $_RLTN_MOTHER       ,       
    '$_RLTN_BRTOHER'      => $_RLTN_BRTOHER      ,      
    '$_RLTN_SISTER'       => $_RLTN_SISTER       ,       
    '$_RLTN_SPOUSE'       => $_RLTN_SPOUSE       ,       
    '$_RLTN_FATHERINLAW'  => $_RLTN_FATHERINLAW  ,  
    '$_RLTN_MOTHERINLAW'  => $_RLTN_MOTHERINLAW  ,  
    '$_RLTN_SON'          => $_RLTN_SON          ,
    '$_RLTN_DAUGHTER'     => $_RLTN_DAUGHTER     ,     
    '$_RLTN_LIVEINPARTNER'=> $_RLTN_LIVEINPARTNER,
    '$_RLTN_OTHERS'       => $_RLTN_OTHERS,

    '$_APPR_STATUS_APPROVED' => $_APPR_STATUS_APPROVED,
    '$_APPR_STATUS_REJECTED' => $_APPR_STATUS_REJECTED,
    '$_APPR_STATUS_INPROGRESS' => $_APPR_STATUS_INPROGRESS,
    '$_APPR_STATUS_NOT_APPROVED' => $_APPR_STATUS_NOT_APPROVED
];
?>