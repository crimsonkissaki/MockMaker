<?php

/**
 * TokenWorker
 *
 * Uses PHP tokens to get information about files.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 28, 2015
 * @version        1.0
 */

namespace MockMaker\Worker;

class TokenWorker
{

    /**
     * Uses PHP tokens to parse a file for use statements
     *
     * Token format for use statements is:
     * use MockMaker\Entities\PropertyWorkerEntity;
     * T_USE(342) T_WHITESPACE(377) T_STRING(308) T_NS_SEPARATOR(386) T_STRING(308) T_NS_SEPARATOR(386) T_STRING(308);
     *
     * @param   string $file File to scan for use statement tokens
     * @return  array
     */
    public function getUseStatementsWithTokens($file)
    {
        $fileTokens = token_get_all(file_get_contents($file));
        $useTokens = [];
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
                break;
            }
        }
        $results = $this->compileTokenIdsIntoString($useTokens, $fileTokens);

        return $results;
    }

    /**
     * Slices specified series of tokens from a full file token list
     *
     * @param   array $tokens     Start/Stop token keys
     * @param   array $fileTokens File token array
     * @return  array
     */
    private function compileTokenIdsIntoString($tokens, $fileTokens)
    {
        $strings = [];
        foreach ($tokens as $token) {
            $offset = $token->startKey;
            $length = ($token->endKey - $token->startKey) + 1;
            $tokensToCompile = array_slice($fileTokens, $offset, $length);
            $strings[] = $this->compileString($tokensToCompile);
        }

        return $strings;
    }

    /**
     * Compiles slice of PHP tokens into a string
     *
     * @param   array $tokens Tokens to combine into single string
     * @return  string
     */
    private function compileString($tokens)
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
