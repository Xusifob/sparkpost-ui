<?php



namespace Xusifob\Sparkpost\Http;

use Symfony\Component\HttpFoundation\Response;


/**
 *
 * Return a CSV Response
 *
 *
 * Class CSVResponse
 * @package Xusifob\Sparkpost\Http
 */
class CSVResponse extends Response
{


	/**
	 *
	 * The data you want to send
	 *
	 * @var array
	 */
	protected $data;


	/**
	 *
	 * The file name
	 *
	 * @var string
	 */
	protected $filename = 'export.csv';


	/**
	 *
	 * The file delimiter
	 *
	 * @var string
	 */
	protected $delimiter = ';';


	/**
	 * CSVResponse constructor.
	 *
	 * @param array $data
	 * @param int $status
	 * @param array $headers
	 */
	public function __construct($data = array(), $status = 200, $headers = array())
	{
		parent::__construct('', $status, $headers);
		$this->setData($data);
	}


	/**
	 *
	 * Set the data inside the response
	 *
	 * @param [] $data
	 *
	 * @return $this
	 */
	public function setData(array $data)
	{
		$output = fopen('php://temp', 'r+');

		if(empty($data) || !isset($data[0])){
			$this->data = '';
			return $this->update();
		}

		$header = array_keys($data[0]);
		fputcsv($output,$header,$this->getDelimiter());

		foreach ($data as $row) {
			if(is_array($row)){
				if(count($header) != count($row)){
					$row = $this->createCorrectArray($header,$row);
				}

				fputcsv($output, $row,$this->getDelimiter());
			}
		}
		rewind($output);
		$this->data = '';
		while ($line = fgets($output)) {
			$this->data .= $line;
		}
		$this->data .= fgets($output);
		return $this->update();
	}


	/**
	 *
	 * Returns the file name
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}


	/**
	 *
	 * @param $header
	 * @param $row
	 * @return array
	 */
	public function createCorrectArray($header,$row)
	{
		$correct = array();

		foreach($header as $key){
			$correct[$key] = isset($row[$key]) ? $row[$key] : '';
		}

		return $correct;
	}


	/**
	 *
	 * Set the file name
	 *
	 * @param $filename
	 *
	 * @return $this
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
		return $this->update();
	}




	/**
	 * @return string
	 */
	public function getDelimiter() {
		return $this->delimiter;
	}


	/**
	 * @param string $delimiter
	 *
	 * @return $this
	 */
	public function setDelimiter( $delimiter ) {
		$this->delimiter = $delimiter;
		return $this;
	}






	/**
	 *
	 * Update the response
	 *
	 * @return $this
	 */
	protected function update()
	{
		$this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));
		if (!$this->headers->has('Content-Type')) {
			$this->headers->set('Content-Type', 'text/csv');
		}
		return $this->setContent($this->data);
	}
}