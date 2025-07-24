<?php
if (! function_exists('paginationPerPage')) {
    /**
     * Retorna o número de itens por página definido na config/pagination.php
     *
     * @return int
     */
    function paginationPerPage() {
        return config('pagination.per_page', 15);
    }
}
