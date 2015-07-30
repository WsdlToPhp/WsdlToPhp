<?php

namespace Api\ServiceType;

use \WsdlToPhp\PackageBase\AbstractSoapClientBase;

/**
 * This class stands for Saint ServiceType
 * @package Api
 * @subpackage Services
 * @release 1.1.0
 */
class ApiSaint extends AbstractSoapClientBase
{
    /**
     * Method to call the operation originally named Saint.CheckJobStatus
     * Meta informations extracted from the WSDL
     * - documentation: Return the current status of a Saint API Job.
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $job_id
     * @return saintresults|bool
     */
    public function Saint_CheckJobStatus($job_id)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.CheckJobStatus', array(
                $job_id,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.CreateFTP
     * Meta informations extracted from the WSDL
     * - documentation: Creates an ftp account for the given parameters and returns the ftp account info
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $description
     * @param string $email
     * @param string $export
     * @param string $overwrite
     * @param string $relation_id
     * @param string $rsid_list
     * @return \Api\StructType\ApiSaint_ftp_info|bool
     */
    public function Saint_CreateFTP($description, $email, $export, $overwrite, $relation_id, $rsid_list)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.CreateFTP', array(
                $description,
                $email,
                $export,
                $overwrite,
                $relation_id,
                $rsid_list,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.ExportCreateJob
     * Meta informations extracted from the WSDL
     * - documentation: Creates Saint Export Job.
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $campaign_filter_begin_range
     * @param string $campaign_filter_end_range
     * @param string $campaign_filter_option
     * @param string $date_filter_row_end_date
     * @param string $date_filter_row_start_date
     * @param string $email_address
     * @param string $encoding
     * @param string $relation_id
     * @param string $report_suite_array
     * @param string $row_match_filter_empty_column_name
     * @param string $row_match_filter_match_column_name
     * @param string $row_match_filter_match_column_value
     * @param string $select_all_rows
     * @param string $select_number_of_rows
     * @return string|bool
     */
    public function Saint_ExportCreateJob($campaign_filter_begin_range, $campaign_filter_end_range, $campaign_filter_option, $date_filter_row_end_date, $date_filter_row_start_date, $email_address, $encoding, $relation_id, $report_suite_array, $row_match_filter_empty_column_name, $row_match_filter_match_column_name, $row_match_filter_match_column_value, $select_all_rows, $select_number_of_rows)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.ExportCreateJob', array(
                $campaign_filter_begin_range,
                $campaign_filter_end_range,
                $campaign_filter_option,
                $date_filter_row_end_date,
                $date_filter_row_start_date,
                $email_address,
                $encoding,
                $relation_id,
                $report_suite_array,
                $row_match_filter_empty_column_name,
                $row_match_filter_match_column_name,
                $row_match_filter_match_column_value,
                $select_all_rows,
                $select_number_of_rows,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.ExportGetFileSegment
     * Meta informations extracted from the WSDL
     * - documentation: Returns the page details of a given file_id
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $file_id
     * @param string $segment_id
     * @return pagedetails|bool
     */
    public function Saint_ExportGetFileSegment($file_id, $segment_id)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.ExportGetFileSegment', array(
                $file_id,
                $segment_id,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.GetCompatabiltyMetrics
     * Meta informations extracted from the WSDL
     * - documentation: Returns Array of compatability information for a report suite(s),
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $report_suite_array
     * @return compatabilitys|bool
     */
    public function Saint_GetCompatabiltyMetrics($report_suite_array)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.GetCompatabiltyMetrics', array(
                $report_suite_array,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.GetFilters
     * Meta informations extracted from the WSDL
     * - documentation: Get SAINT export filters.
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $relation_id
     * @param string $report_suite_array
     * @return export_filters|bool
     */
    public function Saint_GetFilters($relation_id, $report_suite_array)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.GetFilters', array(
                $relation_id,
                $report_suite_array,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.GetTemplate
     * Meta informations extracted from the WSDL
     * - documentation: Returns the template to be used in the SAINT browser or FTP download
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $encoding
     * @param string $numeric_div_nums
     * @param string $relation_id
     * @param string $report_suite
     * @param string $text_div_nums
     * @return string|bool
     */
    public function Saint_GetTemplate($encoding, $numeric_div_nums, $relation_id, $report_suite, $text_div_nums)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.GetTemplate', array(
                $encoding,
                $numeric_div_nums,
                $relation_id,
                $report_suite,
                $text_div_nums,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.ImportCommitJob
     * Meta informations extracted from the WSDL
     * - documentation: Commits a specified Saint Import job for processing.
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $job_id
     * @return string|bool
     */
    public function Saint_ImportCommitJob($job_id)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.ImportCommitJob', array(
                $job_id,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.ImportCreateJob
     * Meta informations extracted from the WSDL
     * - documentation: Creates a Saint Import Job
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $check_divisions
     * @param string $description
     * @param string $email_address
     * @param string $export_results
     * @param string $header
     * @param string $overwrite_conflicts
     * @param string $relation_id
     * @param string $report_suite_array
     * @return int|bool
     */
    public function Saint_ImportCreateJob($check_divisions, $description, $email_address, $export_results, $header, $overwrite_conflicts, $relation_id, $report_suite_array)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.ImportCreateJob', array(
                $check_divisions,
                $description,
                $email_address,
                $export_results,
                $header,
                $overwrite_conflicts,
                $relation_id,
                $report_suite_array,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.ImportPopulateJob
     * Meta informations extracted from the WSDL
     * - documentation: Attaches Import data to a given Saint Import job.
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $job_id
     * @param string $page
     * @param string $rows
     * @return string|bool
     */
    public function Saint_ImportPopulateJob($job_id, $page, $rows)
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.ImportPopulateJob', array(
                $job_id,
                $page,
                $rows,
            )));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Method to call the operation originally named Saint.ListFTP
     * Meta informations extracted from the WSDL
     * - documentation: Returns a list of the ftp accounts configured for this company
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @return saint_ftp_list|bool
     */
    public function Saint_ListFTP()
    {
        try {
            $this->setResult(self::getSoapClient()->__call('Saint.ListFTP'));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Returns the result
     * @see AbstractSoapClientBase::getResult()
     * @return compatabilitys|export_filters|int|pagedetails|saintresults|saint_ftp_list|string|\Api\StructType\ApiSaint_ftp_info
     */
    public function getResult()
    {
        return parent::getResult();
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
