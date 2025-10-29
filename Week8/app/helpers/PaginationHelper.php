<?php

/**
 * Pagination Helper
 * Generates pagination HTML
 */
class PaginationHelper
{
    /**
     * Generate pagination links
     * 
     * @param int $currentPage Current page number
     * @param int $totalPages Total number of pages
     * @param string $baseUrl Base URL for pagination links
     * @param array $params Additional query parameters
     * @return string HTML pagination
     */
    public static function render($currentPage, $totalPages, $baseUrl, $params = [])
    {
        if ($totalPages <= 1) {
            return '';
        }

        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

        // Previous button
        if ($currentPage > 1) {
            $prevParams = array_merge($params, ['page' => $currentPage - 1]);
            $prevUrl = $baseUrl . '?' . http_build_query($prevParams);
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($prevUrl) . '">Previous</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }

        // Page numbers
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);

        // First page
        if ($startPage > 1) {
            $firstParams = array_merge($params, ['page' => 1]);
            $firstUrl = $baseUrl . '?' . http_build_query($firstParams);
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($firstUrl) . '">1</a></li>';
            if ($startPage > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Page numbers around current page
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $pageParams = array_merge($params, ['page' => $i]);
                $pageUrl = $baseUrl . '?' . http_build_query($pageParams);
                $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($pageUrl) . '">' . $i . '</a></li>';
            }
        }

        // Last page
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $lastParams = array_merge($params, ['page' => $totalPages]);
            $lastUrl = $baseUrl . '?' . http_build_query($lastParams);
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($lastUrl) . '">' . $totalPages . '</a></li>';
        }

        // Next button
        if ($currentPage < $totalPages) {
            $nextParams = array_merge($params, ['page' => $currentPage + 1]);
            $nextUrl = $baseUrl . '?' . http_build_query($nextParams);
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($nextUrl) . '">Next</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * Generate pagination info text
     * 
     * @param int $currentPage Current page number
     * @param int $perPage Items per page
     * @param int $totalItems Total items
     * @return string Info text
     */
    public static function info($currentPage, $perPage, $totalItems)
    {
        if ($totalItems == 0) {
            return 'No items found';
        }

        $start = ($currentPage - 1) * $perPage + 1;
        $end = min($currentPage * $perPage, $totalItems);

        return "Showing $start to $end of $totalItems entries";
    }
}
