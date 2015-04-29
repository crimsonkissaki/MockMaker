<?php

/**
 * 	TokenWorker
 *
 *  Uses tokens to get information about files.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

class TokenWorker
{

    /**
     * Use PHP tokens to parse a file for its use statements.
     *
     * Token format for use statements is:
     * use MockMaker\Entities\PropertyWorkerEntity;
     * T_USE(342) T_WHITESPACE(377) T_STRING(308) T_NS_SEPARATOR(386) T_STRING(308) T_NS_SEPARATOR(386) T_STRING(308);
     *
     * @param   $file   string
     * @return  array
     */
    public function getUseStatementsWithTokens($file)
    {
        $fileTokens = token_get_all(file_get_contents($file));
        $useTokens = [ ];
        $useTokenKey = 0;
        $foundUseToken = false;
        foreach ($fileTokens as $key => $token) {
            if (is_array($token) && $token[0] === 342) {
                $foundUseToken = true;
                $useTokens[$useTokenKey] = new \stdClass();
                $useTokens[$useTokenKey]->startKey = $key;
            }
            if ($token === ';' && $foundUseToken) {
                $foundUseToken = false;
                $useTokens[$useTokenKey]->endKey = $key;
                $useTokenKey += 1;
            }
            if (is_array($token) && $token[0] === 356) {
                // T_CLASS token.
                break;
            }
        }
        $results = $this->compileTokenIdsIntoString($useTokens, $fileTokens);

        return $results;
    }

    public function compileTokenIdsIntoString($tokens, $fileTokens)
    {
        $strings = [ ];
        foreach ($tokens as $k => $token) {
            $offset = $token->startKey;
            $length = ($token->endKey - $token->startKey) + 1;
            $tokensToCompile = array_slice($fileTokens, $offset, $length);
            $strings[] = $this->compileString($tokensToCompile);
        }

        return $strings;
    }

    public function compileString($tokens)
    {
        $string = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                $string .= $token[1];
            } else {
                $string .= $token;
            }
        }

        return $string;
    }

}
