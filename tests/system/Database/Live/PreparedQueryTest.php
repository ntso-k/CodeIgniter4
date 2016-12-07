<?php namespace CodeIgniter\Database\Live;

use CodeIgniter\Database\BasePreparedQuery;

class PreparedQueryTest extends \CIDatabaseTestCase
{
	protected $refresh = true;

	protected $seed = 'CITestSeeder';

	//--------------------------------------------------------------------

	public function testPrepareReturnsPreparedQuery()
	{
	    $query = $this->db->prepare(function($db){
	    	return $db->table('user')->insert([
	    		'name' => 'a',
				'email' => 'b@example.com'
			]);
		});

		$this->assertTrue($query instanceof BasePreparedQuery);

		$ec = $this->db->escapeChar;
		$pre = $this->db->DBPrefix;

		$placeholders = '?, ?';

		if ($this->db->DBDriver == 'Postgre')
		{
			$placeholders = '$1, $2';
		}

		$expected = "INSERT INTO {$ec}{$pre}user{$ec} ({$ec}name{$ec}, {$ec}email{$ec}) VALUES ({$placeholders})";
		$this->assertEquals($expected, $query->getQueryString());

		$query->close();
	}

	//--------------------------------------------------------------------

    /**
     * @group single
     */
	public function testExecuteRunsQueryAndReturnsResultObject()
	{
		$query = $this->db->prepare(function($db){
			return $db->table('user')->insert([
				'name' => 'a',
				'email' => 'b@example.com',
				'country' => 'x'
			]);
		});

		$query->execute('foo', 'foo@example.com', 'US');
		$query->execute('bar', 'bar@example.com', 'GB');

		$this->seeInDatabase($this->db->DBPrefix.'user', ['name' => 'foo', 'email' => 'foo@example.com']);
		$this->seeInDatabase($this->db->DBPrefix.'user', ['name' => 'bar', 'email' => 'bar@example.com']);

		$query->close();
	}

	//--------------------------------------------------------------------

}
