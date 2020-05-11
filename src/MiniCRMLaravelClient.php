<?php


namespace MiniCRMLaravel;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class MiniCRMLaravelClient
 *
 * Description
 * Publish files php artisan vendor:publish --provider="MiniCRMLaravel\MiniCRMServiceProvider" --tag=minicrm
 *
 * @package MiniCRMLaravel
 */
class MiniCRMLaravelClient {

	/** @var HttpClient */
	private $client;

	/**
	 * @var string
	 */
	private $systemId;

	/**
	 * @var string
	 */
	private $apiKey;

	/**
	 * @var string
	 */
	private $apiUrlBase;


	/**
	 * MiniCRMLaravelClient constructor.
	 *
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		if (strlen(config('minicrm.APIKEY')) != 32) {
			throw new Exception('Invalid API key!');
		}

		$this->systemId = config('minicrm.SYSTEMID');
		$this->apiKey = config('minicrm.APIKEY');
		$this->apiUrlBase = "https://{$this->systemId}:{$this->apiKey}@r3.minicrm.hu/Api/R3/";
	}

	public function connect()
	{
		$this->client = new Client();
	}

	/**
	 * @param  array  $contactData
	 *
	 * @return mixed
	 */
	public function createContact(array $contactData)
	{
		try {
			$options = [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				],
				'json'    => $contactData,
			];

			$contact = $this->client->request('PUT', $this->apiUrlBase."Contact", $options);

			return json_decode($contact->getBody()->getContents());
		} catch (GuzzleException $exception) {
			echo $exception->getMessage();
		}
	}

	/**
	 * @param  array  $projectData
	 *
	 * @return mixed
	 */
	public function createProject(array $projectData)
	{
		try {
			$options = [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				],
				'json'    => $projectData,
			];

			$project = $this->client->request('PUT', $this->apiUrlBase."Project", $options);

			return json_decode($project->getBody()->getContents());
		} catch (GuzzleException $exception) {
			echo $exception->getMessage();
		}
	}

	/**
	 * @param  bool  $detailed
	 *
	 * @return mixed
	 */
	public function getCategories(bool $detailed = false)
	{
		try {
			$segment = $detailed ? "Category?Detailed=1" : "Category";

			$categories = $this->client->request('GET', $this->apiUrlBase.$segment);

			return json_decode($categories->getBody()->getContents());
		} catch (GuzzleException $exception) {
			echo $exception->getMessage();
		}
	}

	/**
	 * @param  int  $projectId
	 *
	 * @return mixed
	 */
	public function getProject(int $projectId)
	{
		try {
			$project = $this->client->request('GET', $this->apiUrlBase."Project/".$projectId);

			return json_decode($project->getBody()->getContents());
		} catch (GuzzleException $exception) {
			echo $exception->getMessage();
		}
	}

	/**
	 * @param  string  $type
	 * @param  int|null  $projectId
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function getSchema(string $type, int $projectId = null)
	{
		$types = ['Project', 'Business', 'Person'];

		if ( !in_array($type, $types)) {
			throw new Exception("Invalid type {$type}!");
		}

		if ($type === 'Project' && !$projectId) {
			throw new Exception('Project típus esetén kötelező a project id!');
		}

		try {
			$segment = $projectId ? "Schema/Project/{$projectId}" : "Schema/".ucfirst($type);

			$schema = $this->client->request('GET', $this->apiUrlBase.$segment);

			return json_decode($schema->getBody()->getContents());
		} catch (GuzzleException $exception) {
			echo $exception->getMessage();
		}
	}
}
