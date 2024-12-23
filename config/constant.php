<?php

$_YES = 1;
$_NO = 2;

$_GENDER_MALE = 1;
$_GENDER_FEMALE = 2;
$_GENDER_OTHER = 3;

$_RLTN_SELF         = 1;
$_RLTN_FATHER       = 2;
$_RLTN_MOTHER       = 3;
$_RLTN_BROTHER      = 4;
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

$_TITLE_MR = 1;
$_TITLE_MS= 2;
$_TITLE_MRS = 3;

$selectLabel = [-1 => '--Select--'];

/****************** Relationship Types ******************/
$relationship_types = [ 
    $_RLTN_SELF => 'Self',
    $_RLTN_FATHER => 'Father',
    $_RLTN_MOTHER => 'Mother',
    $_RLTN_BROTHER => 'Brother',
    $_RLTN_SISTER => 'Sister',
    $_RLTN_SPOUSE => 'Spouse',
    $_RLTN_FATHERINLAW => 'Father-in-Law',
    $_RLTN_MOTHERINLAW=> 'Mother-in-Law',
    $_RLTN_SON => 'Son',
    $_RLTN_DAUGHTER => 'Daughter',
    $_RLTN_LIVEINPARTNER => 'Live-In Partner',
    $_RLTN_OTHERS => 'Others'
];
$relationshipDep_types = [ 
    $_RLTN_FATHER => 'Father',
    $_RLTN_MOTHER => 'Mother',
    $_RLTN_BROTHER => 'Brother',
    $_RLTN_SISTER => 'Sister',
    $_RLTN_SPOUSE => 'Spouse',
    $_RLTN_FATHERINLAW => 'Father-in-Law',
    $_RLTN_MOTHERINLAW=> 'Mother-in-Law',
    $_RLTN_SON => 'Son',
    $_RLTN_DAUGHTER => 'Daughter',
    $_RLTN_LIVEINPARTNER => 'Live-In Partner',
    $_RLTN_OTHERS => 'Others'
];
$relationship_typesLE = [ 
    $_RLTN_SPOUSE => 'Spouse',
    $_RLTN_SON => 'Son',
    $_RLTN_DAUGHTER => 'Daughter',
];
/******* NON-MULTIPLE RELATIONS ******/
$relationshipNonDuplicate_types = [ 
    $_RLTN_FATHER => 'Father',
    $_RLTN_MOTHER => 'Mother',
    $_RLTN_SPOUSE => 'Spouse',
    $_RLTN_FATHERINLAW => 'Father-in-Law',
    $_RLTN_MOTHERINLAW=> 'Mother-in-Law',
    $_RLTN_LIVEINPARTNER => 'Live-In Partner',
];
/******* NON-MULTIPLE RELATIONS ******/


$titleName = [ 
    $_TITLE_MR => 'Mr.',
    $_TITLE_MS => 'Ms.',
    $_TITLE_MRS => 'Mrs.',
];

function generateJtableOptions($arr1,$arr2) {
    foreach (($arr1 + $arr2) as $k => $v) {
        $generatedOptions[$k] = "{Value:'" . $k . "',DisplayText:'" . $v . "'}";
    }
    return $generatedOptions;
}
/******* GENDER *******/
$gender = [$_GENDER_MALE => 'Male',$_GENDER_FEMALE => 'Female', $_GENDER_OTHER => 'Others'];
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

/******* Dependant Code *******/
$dependant_code = [ // these values are indexes/keys of relationship_types
    'E' => [1],
    'S' => [6],
    'P' => [2,3],
    'PIL' => [7,8],
    'C' => [9,10],
    'L' => [11],
    'O' => [12, 4,5]
    
];
/******* Dependant Code *******/

/******* Dependant Code UI *******/
$dependant_code_ui = [ // these values are indexes/keys of relationship_types
    'E' => 'Employee',
    'S' => 'Spouse',
    'PIL' => 'Parent-In-Law',
    'P' => 'Parents',
    'C' => 'Children',
    'L' => 'Live-In Partner',
    'O' => 'Others',
    '/' => 'OR'
    
];
/******* Dependant Code *******/

/******* Salary ********/
// $salary = Auth::user()->salary;
// $encryptedData =  Auth::user()->salary;
// $encryptionKey = 'QCsmMqMwEE+Iqfv0IIXDjAqrK4SOSp3tZfCadq1KlI4=';
// $initializationVector = 'G4bfDHjL3gXiq5NCFFGnqQ==';

// // Decrypt the data
// $cipher = "aes-256-cbc";
// $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

// $salary = openssl_decrypt(base64_decode($encryptedData), $cipher, base64_decode($encryptionKey), $options, base64_decode($initializationVector));

// if ($salary === false) {
//     echo "Error during decryption: " . openssl_error_string() . PHP_EOL;
// } else {
//     $salary = floatval(rtrim($salary, "\0"));
// }
/***********************/

return [
    // relationship array
    'relationship_type' => $relationship_types,
    'relationship_typeLE' => $relationship_typesLE,
    'dependant_code_ui' => $dependant_code_ui,
    'relationshipNonDuplicate_types' => $relationshipNonDuplicate_types,
    'relationship_type_jTable' => implode(',', generateJtableOptions($selectLabel,$relationship_types)),
    'relationshipDep_type_jTable' => generateJtableOptions($selectLabel,$relationshipDep_types),
    'gender_jTable' => implode(',', generateJtableOptions($selectLabel,$gender)),
    'boolean_jTable' => implode(',', generateJtableOptions($selectLabel,$boolean)),
    'booleanArr' => $boolean,
    'approval_status_jTable' => implode(',', generateJtableOptions($selectLabel,$approval_status)),
    'approval_status' => $approval_status,
    'dependant_code' => $dependant_code,
    'relationshipLE_type_jTable' => generateJtableOptions($selectLabel,$relationship_typesLE),
    'title' => $titleName,

    'gender' => $gender,
    '$_GENDER_MALE' => $_GENDER_MALE,
    '$_GENDER_FEMALE' => $_GENDER_FEMALE,
    '$_GENDER_OTHER' => $_GENDER_OTHER,

    '$_YES' => $_YES,
    '$_NO' => $_NO,

    '$_RLTN_SELF'         => $_RLTN_SELF         ,         
    '$_RLTN_FATHER'       => $_RLTN_FATHER       ,       
    '$_RLTN_MOTHER'       => $_RLTN_MOTHER       ,       
    '$_RLTN_BROTHER'      => $_RLTN_BROTHER      ,      
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