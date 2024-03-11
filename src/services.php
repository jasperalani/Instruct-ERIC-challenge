<?php declare(strict_types=1);

/*
 * Instruct Junior PHP Developer Code Challenge
 * Code by Jasper Alani
 */

// This will show in place of empty cells
$no_data = '{NO_DATA}';

// Check csv file exists
if(!file_exists('services.csv')){
    echo 'Missing required services.csv file';
}

if(count($argv) === 1){
    echo "With services.php you can either query the corresponding csv file or request a summary of the file.\n";
    query_command_info();
    echo "\n";
    summary_command_info();
    exit;
}

// Read first (actually second) argument
$arg1 = $argv[1];

$function = 'unset';

// Determine whether the user wants to query or request a summary of the data
if(in_array($arg1, ['query', 'summary'])){
    $function = $arg1;
}

if($function == 'unset'){
    // User did not enter a valid argument
    query_command_info();
    exit;
}

switch($function){
    case 'query':
        if(!array_key_exists(2, $argv)){
            query_command_info();
            exit;
        }

        // Read second (actually third) argument
        $country_code_argument = strtolower($argv[2]) ?? 'unset';

        // Load the csv into a multi-dimensional array
        $data = load_csv();

        $query_results = [];

        // Loop through the array
        foreach($data as $row){
            $country_code = strtolower($row[3]) ?? $no_data;

            // Check if the country code supplied is found in the row
            if($country_code === $country_code_argument){
                $ref = $row[0] ?? $no_data;
                $centre = $row[1] ?? $no_data;
                $service = $row[2] ?? $no_data;

                // Build the string that is displayed
                $query_results[] = "\nRef: $ref\nCentre: $centre\nService: $service";
            }
        }

        if(empty($query_results)){
            echo "No data matched the supplied country code: $country_code_argument";
            break;
        }

        // Show the amount of results and what the queried code was
        echo "Query found " . count($query_results) . " results for supplied country code: $country_code_argument\n";

        $count = 1;
        foreach($query_results as $query_result){

            echo $query_result;

            // Only add a newline if not the last result
            if($count !== count($query_results)){
                echo "\n";
            }

            $count++;
        }

        // Query is complete and data has been returned.
        break;
    case 'summary':
        // Show amount of services per country

        $data = load_csv();
        $summary_data = [];
        foreach($data as $row){
            $ref = $row[0] ?? $no_data;
            $centre = $row[1] ?? $no_data;
            $service = $row[2] ?? $no_data;
            $country = strtolower($row[3]) ?? $no_data;

            $summary_data[$country][] = $service;
        }

        echo "Summary:";

        foreach($summary_data as $country => $services){
            $amount_of_services = count($services);
            $plural = $amount_of_services > 1 ? 's' : '';
            echo "\nCountry code $country has $amount_of_services related service$plural.";
        }
        break;
    case 'unset':
    default:

}

function load_csv(){
    // Map the str_getcsv function to each line of services.csv creating a multi-dimensional array representation of the csv
    $csv = array_map('str_getcsv', file('services.csv'));
    unset($csv[0]); // Remove the headings
    return $csv;
}

function query_command_info(){
    echo "If you want to query please provide the word \"query\" as the first argument and the country code you would like to query as the second argument. Please note the country code is not case-specific.";
    echo "\nExample: php services.php query gb";
}

function summary_command_info(){
    echo "If you would like to request a summary, then provide the word \"summary\" as the first argument.";
    echo "\nExample: php services.php summary";
}