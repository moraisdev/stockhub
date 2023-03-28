<?php

namespace App\Services;

class FilesService{

    protected static function sanitizeCsvArrayKeys(array $keys_array){
        foreach ($keys_array as $index => $key) {
            $clear_key = strtolower($key);

            $special_chars_array = ['(', ')', ' /', '/'];

            $clear_key = str_replace($special_chars_array, '', $clear_key);
            $clear_key = str_replace(' ', '_', $clear_key);

            $keys_array[$index] = $clear_key;
        }

        return $keys_array;
    }

    public static function csvToArray(string $csv_path){
    	/* Open CSV File */
    	$csv_file = fopen($csv_path, 'r');

        $products_array_keys = [];
        $products = [];

        $first_while_execution = true;

        while (($array_line = fgetcsv($csv_file)) !== FALSE) {
			if($first_while_execution){
				/* Save products array keys (first csv_file line) */
				$products_array_keys = self::sanitizeCsvArrayKeys($array_line);
			}else{
				/* Save the csv line in the products array with the right keys */
				$products[] = array_combine($products_array_keys, $array_line);
			}

			$first_while_execution = false;
        }

        /* Close CSV File */
        fclose($csv_file);

        return $products;
    }
}