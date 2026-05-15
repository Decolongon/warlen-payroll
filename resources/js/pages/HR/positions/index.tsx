import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { X, UserCog, Plus, Search, Loader2 } from 'lucide-react';
import { useState, useMemo, useEffect, useRef } from 'react';
import { TableSearchHeader } from '@/components/table-search-header';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/hr-layout';
import type { BreadcrumbItem } from '@/types';
import { CustomHeader } from '@/components/custom-header';
import { CustomTable } from '@/components/custom-table';
import { CustomPagination } from '@/components/custom-pagination';
import { PositionTableConfig } from '@/config/tables/position-table';
import PositionController from '@/actions/App/Http/Controllers/PositionController';
import { toast } from 'sonner';
import { DeleteConfirmationDialog } from '@/components/delete-confirmation-modal';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Positions', href: '/hr/positions' },
];

interface Position {
    id: number;
    pos_name: string;
    basic_salary: number;
    pos_slug: string;
    is_salary_fixed: boolean;
}

interface LinkItem {
    active: boolean;
    label: string;
    url: string | null;
}

interface PositionPagination {
    data: Position[];
    links: LinkItem[];
    from: number;
    to: number;
    total: number;
    current_page?: number;
}

interface FilterProps {
    search: string;
    perPage: string;
}

interface IndexProps {
    positions: PositionPagination;
    filters: FilterProps;
    totalCount: number;
    filteredCount: number;
}

const toastStyle = (color: string) => ({
    style: {
        backgroundColor: 'white',
        color: color,
        border: '1px solid #e2e8f0',
        boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)',
    },
});

export default function Index({
    positions,
    filters = { search: '', perPage: '10' },
    totalCount,
    filteredCount,
}: IndexProps) {
    const { delete: destroy } = useForm();
    const { props } = usePage<{ flash?: { success?: string; error?: string; warning?: string; info?: string } }>();

    const { data, setData } = useForm({
        search:  filters?.search  || '',
        perPage: filters?.perPage || '10',
    });

    // ── Clear filters loading state ──────────────────────────────────────────
    const [isClearingFilters, setIsClearingFilters] = useState(false);
    const isClearingRef = useRef(false);
    const clearFiltersTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
    
    // Store last filter values for display during clearing
    const [lastSearchTerm, setLastSearchTerm] = useState('');

    // Prevent duplicate toasts within a 500ms window
    const lastFlashRef = useRef<{ key: string; time: number }>({ key: '', time: 0 });

    useEffect(() => {
        const flash = props.flash;
        if (!flash) return;

        const flashKey = JSON.stringify(flash);
        const now      = Date.now();
        const last     = lastFlashRef.current;

        if (last.key === flashKey && now - last.time < 500) return;
        lastFlashRef.current = { key: flashKey, time: now };

        if (flash.success) toast.success(flash.success, toastStyle('#16a34a'));
        if (flash.error)   toast.error(flash.error,     toastStyle('#dc2626'));
        if (flash.warning) toast.warning(flash.warning, toastStyle('#f97316'));
        if (flash.info)    toast.info(flash.info,       toastStyle('#3b82f6'));
    }, [props.flash]);

    // Only transform display values — do NOT add a search filter here.
    // The server already returns the correct page of results. Filtering
    // the already-paginated slice client-side makes pagination do nothing.
    const transformedPositions = useMemo(() =>
        positions.data.map(p => ({
            ...p,
            is_salary_fixed_display: p.is_salary_fixed ? 'Fixed' : 'Not Fixed',
        })),
        [positions.data],
    );

    // ── Shared navigation helper ──────────────────────────────────────────────
    // Always sends ALL active params (search + perPage + page) so none are
    // dropped when only one value changes.
    const navigate = (overrides: Record<string, string>) => {
        // Don't navigate while clearing filters
        if (isClearingRef.current) return;
        
        const params: Record<string, string> = {};
        if (data.search)  params.search  = data.search;
        if (data.perPage) params.perPage = data.perPage;

        // Merge overrides — caller can clear a key by passing ''
        Object.assign(params, overrides);

        // Strip empty strings so they don't pollute the URL
        const clean = Object.fromEntries(
            Object.entries(params).filter(([, v]) => v !== ''),
        );

        router.get('/hr/positions', clean, {
            preserveState:  true,
            preserveScroll: true,
        });
    };

    // ── Handlers ──────────────────────────────────────────────────────────────

    const handleSearchChange = (value: string) => {
        if (isClearingRef.current) return;
        setData('search', value);
        navigate({ search: value, page: '' }); // reset to page 1 on new search
    };

    const handleSearchReset = () => {
        // Store current search term before clearing
        setLastSearchTerm(data.search);
        
        // Clear any pending timers
        if (clearFiltersTimer.current) {
            clearTimeout(clearFiltersTimer.current);
        }
        
        // Set clearing flag
        isClearingRef.current = true;
        setIsClearingFilters(true);
        
        // Reset filter states immediately
        setData('search', '');
        setData('perPage', '10');
        
        // Navigate with cleared filters
        router.get('/hr/positions', {}, {
            preserveScroll: true,
            onFinish: () => {
                clearFiltersTimer.current = setTimeout(() => {
                    isClearingRef.current = false;
                    setIsClearingFilters(false);
                    // Clear stored filters after a delay
                    setTimeout(() => {
                        setLastSearchTerm('');
                    });
                });
            }
        });
    };

    const handlePerPageChange = (value: string) => {
        if (isClearingRef.current) return;
        setData('perPage', value);
        navigate({ perPage: value, page: '' }); // reset to page 1 on perPage change
    };

    // CustomPagination calls onPageChange(pageNumber: number)
    const handlePageChange = (page: number) => {
        if (isClearingRef.current) return;
        navigate({ page: String(page) });
    };

    const handleEditClick = (position: Position) => {
        if (isClearingRef.current) return;
        router.get(PositionController.edit(position.pos_slug).url);
    };

    // ── Delete dialog ─────────────────────────────────────────────────────────

    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [positionToDelete, setPositionToDelete] = useState<Position | null>(null);
    const [isDeleting, setIsDeleting]             = useState(false);

    const handleDeleteClick = (position: Position) => {
        setPositionToDelete(position);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (!positionToDelete) return;
        setIsDeleting(true);

        destroy(PositionController.destroy(positionToDelete.pos_slug).url, {
            onSuccess: () => {
                setDeleteDialogOpen(false);
                setPositionToDelete(null);
            },
            onError: (errors) => {
                const msg = (Object.values(errors).flat()[0] as string) || 'Failed to delete position.';
                toast.error(msg, toastStyle('#dc2626'));
            },
            onFinish: () => setIsDeleting(false),
        });
    };

    // ── Derived flags ─────────────────────────────────────────────────────────

    const hasActiveFilters = !!data.search.trim();
    const showFilterEmptyState = hasActiveFilters || isClearingFilters || (lastSearchTerm && lastSearchTerm.trim() !== '');
    
    // Helper to format filter display text
    const getFilterDisplayText = () => {
        if (isClearingFilters && lastSearchTerm) {
            return `No positions matching "${lastSearchTerm}".`;
        }
        if (data.search) {
            return `No positions matching "${data.search}".`;
        }
        return 'No positions match your current filters.';
    };

    // ── Column config ─────────────────────────────────────────────────────────

    const updatedColumns = PositionTableConfig.columns.map(col =>
        col.key === 'is_salary_fixed'
            ? { ...col, render: (row: any) => <span>{row.is_salary_fixed_display}</span> }
            : col,
    );

    // ── Render ────────────────────────────────────────────────────────────────

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Positions" />

            <style>{`
                @keyframes fadeUp {
                    from { opacity: 0; transform: translateY(16px); }
                    to   { opacity: 1; transform: translateY(0); }
                }
                .pp-row { animation: fadeUp 0.3s cubic-bezier(0.22,1,0.36,1) both; }
                @keyframes headerReveal {
                    from { opacity: 0; transform: translateY(-10px); }
                    to   { opacity: 1; transform: translateY(0); }
                }
                .pp-header { animation: headerReveal 0.35s cubic-bezier(0.22,1,0.36,1) both; }
            `}</style>

            <div className="flex flex-col gap-4 p-4 min-h-[calc(85vh-48px)] mx-4">

                {/* Header with title */}
                <div className="flex justify-between items-center pp-header">
                    <CustomHeader
                        title="Positions"
                        description="Manage job positions and their corresponding basic salaries."
                        icon={<UserCog />}
                    />

                    {positions.total >= 1 && (
                        <Link href="/hr/positions/create">
                            <Button size="sm"><Plus /> Add Position</Button>
                        </Link>
                    )}
                </div>

                <div className="flex flex-col gap-4 pp-row">
                    <CustomTable
                        title="Position Lists"
                        columns={updatedColumns}
                        actions={PositionTableConfig.actions}
                        data={transformedPositions}
                        from={positions.from ?? 1}
                        onDelete={handleDeleteClick}
                        onView={() => { }}
                        onEdit={handleEditClick}
                        hasActiveFilters={hasActiveFilters}
                        searchTerm={data.search}
                        toolbar={
                            <TableSearchHeader
                                searchValue={data.search}
                                onSearchChange={handleSearchChange}
                                onSearchReset={handleSearchReset}
                                searchPlaceholder="Search positions..."
                            />
                        }
                        emptyState={
                            showFilterEmptyState ? (
                                <div className="flex flex-col items-center justify-center py-12 px-6 text-center">
                                    <div className="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                        <Search className="h-5 w-5 text-slate-400 dark:text-slate-500" />
                                    </div>
                                    <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                        No results found
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 mb-4 max-w-xs">
                                        {getFilterDisplayText()}
                                    </p>
                                    <Button 
                                        variant="outline" 
                                        size="sm" 
                                        onClick={handleSearchReset}
                                        disabled={isClearingFilters}
                                        className="rounded-xl border-2 border-border px-4 py-2 text-sm font-semibold text-foreground transition-all hover:border-primary hover:text-primary active:scale-95 cursor-pointer"
                                    >
                                        {isClearingFilters ? (
                                            <>
                                                <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                                                Clearing filters...
                                            </>
                                        ) : (
                                            'Clear filters'
                                        )}
                                    </Button>
                                </div>
                            ) : (
                                <div className="flex flex-col items-center justify-center py-12 px-6 text-center">
                                    <div className="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                        <UserCog className="h-5 w-5 text-slate-500 dark:text-slate-400" />
                                    </div>
                                    <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">No positions yet.</h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 mb-4 max-w-xs">
                                        Get started by creating your first position.
                                    </p>
                                    <Button className='cursor-pointer' size="sm" onClick={() => router.get('/hr/positions/create')}>
                                        <Plus className="h-4 w-4 mr-2" /> Create Position
                                    </Button>
                                </div>
                            )
                        }
                    />

                    {positions.total >= 1 && (
                        <CustomPagination
                            pagination={positions}
                            perPage={data.perPage}
                            onPerPageChange={handlePerPageChange}
                            onPageChange={handlePageChange}
                            totalCount={totalCount}
                            filteredCount={filteredCount}
                            search={data.search}
                            resourceName='position'
                        />
                    )}

                    {/* Delete Confirmation Dialog */}
                    <DeleteConfirmationDialog
                        isOpen={deleteDialogOpen}
                        onClose={() => {
                            setDeleteDialogOpen(false);
                            setPositionToDelete(null);
                        }}
                        onConfirm={confirmDelete}
                        title="Delete Position"
                        itemName={positionToDelete?.pos_name || ''}
                        isLoading={isDeleting}
                        confirmText="Delete Position"
                    />
                </div>
            </div>
        </AppLayout>
    );
}