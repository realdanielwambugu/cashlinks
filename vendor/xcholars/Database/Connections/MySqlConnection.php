<?php

Namespace Xcholars\Database\Connections;

use Xcholars\Database\Query\Grammars\MySqlGrammar as QueryGrammar;

use Xcholars\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;

use Xcholars\Database\Query\Processors\MySqlProcessor as Processor;


class MySqlConnection extends Connection 
{
   /**
    * Get the default query grammar instance.
    *
    * @return object Xcholars\Database\Query\Grammars\MySqlGrammar
    */
    protected function setQueryGrammar()
    {
        return $this->queryGrammar = new QueryGrammar;
    }

    /**
    * Get the default post processor instance.
    *
    * @return object Xcholars\Database\Query\Processors\MySqlProcessor
    */
    protected function setPostProcessor()
    {
        return $this->processor = new Processor;
    }

   /**
    * Set the schema grammar to the default implementation.
    *
    * @return void
    */
    protected function setSchemaGrammar()
    {
       return $this->schemaGrammar = new SchemaGrammar;
    }

}
