<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

class ErogamescapePortal extends Controller
{
	protected function getErogeByGameId($egs_game_id) {
		$egs_game_id = 23994;
		if($egs_game_id) {
			$params = array(
				'game_id' => $egs_game_id
			);
			$query_to_retrieve = $this->parseParamToQuery($params);
			$html_content = $this->retrievePageContent($query_to_retrieve);
			$this->writeHtmlToDisk($html_content, storage_path('/app/egs-sql-html-log/'), 'egs_sql_');
			$data = $this->extractEssenceFromHtml($html_content);
			return response()->json(['data' => $data]);
		}
	}
	protected function searchEroge($search_query) {
		if($search_query) {
			$params = array(
				// 'game_id' => $egs_game_id,
				'search_query' => $search_query
			);
			$query_to_retrieve = $this->parseParamToQuery($params);
			$html_content = $this->retrievePageContent($query_to_retrieve);
			$this->writeHtmlToDisk($html_content, storage_path('/app/egs-sql-html-log/'), 'egs_sql_');
			$data = $this->extractEssenceFromHtml($html_content);
			return response()->json(['data' => $data]);
		}
	}
	protected function retrievePageContent($query) {
		$fields = array(
			'sql' => $query
		);
		$postvars = '';
		$sep = ' ';
		foreach ($fields as $key => $value) {
			$postvars = $sep . urlencode($key) . '=' .urlencode($value);
			$sep = '&';
		}

		$ch = curl_init();
		$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
		$egs_sql_url = 'http://erogamescape.dyndns.org/~ap2/ero/toukei_kaiseki/sql_for_erogamer_form.php';

		curl_setopt($ch, CURLOPT_URL, $egs_sql_url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return $result;
	}
	protected function parseParamToQuery($params, $target_table = null) {

		$sql = 'SELECT * FROM ';
		switch ($target_table) {
			case 'game':
				$sql = $sql . 'gamelist ';
				break;
			case 'developer':
				$sql = $sql . 'brandlist ';
				break;
			default:
				$sql = $sql . 'gamelist ';
				break;
		}

		if($target_table == null || $target_table == 'game') {
			$sql = $sql . 'LEFT JOIN brandlist ON gamelist.brandname = brandlist.id ';
		}

		if(isset($params['game_id'])) {
			$sql = $sql . 'WHERE gamelist.id = \'' . $params['game_id'] . '\' ';
		}
		else if(isset($params['search_query'])) {
			$sql = $sql . "WHERE gamelist.id > '0' AND (gamelist.gamename LIKE '%" . $params['search_query'] . "%' ";
			$search_keywords = explode(' ', $params['search_query']);
			foreach ($search_keywords as $word) {
				$sql = $sql . "OR gamelist.gamename LIKE '%" . $word . "%' ";
				$sql = $sql . "OR gamelist.furigana LIKE '%" . $word . "%' ";
				$sql = $sql . "OR brandlist.brandname LIKE '%" . $word . "%' ";
			}
			$sql = $sql . ') ';
		}
		return $sql;
	}
	protected function extractEssenceFromHtml($html) {
		$html = preg_replace('/\s+/S', " ", $html);
		$essence = array();

		$dom = new \DOMDocument();
		libxml_use_internal_errors(true);
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		// Converting broken UTF-8 into its proper encoding
		$html = mb_convert_encoding($html , 'HTML-ENTITIES', 'UTF-8');
		$dom->loadHTML($html);

		$sql_result_key = array();
		// $sql_result_value = array();
		$sql_result_complete = array();

		// Parse element of div with id=query_result_main
		$element_of_queryresultmain = $dom->getElementById('query_result_main');
		$innerhtml_of_queryresultmain = $dom->saveHtml($element_of_queryresultmain);
		// Parse element of table
		$element_of_table = $element_of_queryresultmain->getElementsByTagName('tr');
		// Assign each value in th tag into array
		$element_of_th = $element_of_table[0]->getElementsByTagName('th');
		foreach ($element_of_th as $th_object) {
			$sql_result_key[] = $th_object->nodeValue;
		}
		// Assign each value in td tag for each row into array
		$i = 0;
		foreach ($element_of_table as $key => $tr_element) {
			if($i > 0) {
				$sql_result_value = array();
				$element_of_td = $tr_element->getElementsByTagName('td');
				foreach ($element_of_td as $key => $td_object) {
					$sql_result_value[] = $td_object->nodeValue;
				}
				$sql_result_complete[] = array_combine($sql_result_key, $sql_result_value);
			}
			$i++;
		}

		if($only_one_row = false == true) {
			$element_of_td = $element_of_table[1]->getElementsByTagName('td');
			foreach ($element_of_td as $td_object) {
				$sql_result_value[] = $td_object->nodeValue;
			}
			$sql_result_complete = array_combine($sql_result_key, $sql_result_value);
		}

		return $sql_result_complete;
	}

	protected function writeHtmlToDisk($content, $directory, $prefix = null) {
		$date = date('YmdHis');
		$this->prepareDirectory($directory);
		$path = $directory . '/';
		if($prefix) {
		 $path = $path . $prefix;
		}
		$path = $path . $date . '.html';
		$file = fopen($path, 'w');
		fwrite($file, $content);
		fclose($file);
		return true;
	}

	protected function prepareDirectory($directory) {
		try {
			if(!is_dir($directory)) {
				mkdir($directory, 0777, true);
			}
		}
		catch(\Exception $e) {
			throw new \Symfony\Component\HttpKernel\Exception\HttpException('Directory preparation failed!');
		}

		return true;
	}
}
