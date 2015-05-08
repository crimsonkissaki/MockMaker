<?php
/**
 * TemplateWorker
 *
 * Handles dataPoint aggregation and merging with template files.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/8/15
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\DataContainer;

class TemplateWorker
{

    /**
     * Processes a DataContainer object with a DataPointWorker
     *
     * @param   DataContainer           $dataContainer
     * @param   AbstractDataPointWorker $dataPointWorker
     * @return  string
     */
    public function processWithWorker(AbstractDataPointWorker $dataPointWorker, DataContainer $dataContainer)
    {
        $dataPoints = $dataPointWorker->generateDataPoints($dataContainer);
        $code = StringFormatterWorker::vsprintf2($dataPointWorker->getTemplateContents(), $dataPoints);
        $dataPointWorker->processCode($dataContainer, $code);

        return $code;
    }
}