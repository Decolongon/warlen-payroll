import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { Building2, Search, X, Loader2 } from 'lucide-react';
import { useState, useEffect, useRef } from 'react';
import BranchController from "@/actions/App/Http/Controllers/HrRole/HRBranchController";
import { CustomHeader } from '@/components/custom-header';
import { CustomTable } from '@/components/custom-table';
import { toast } from 'sonner';
import { EmployeeFilterBar } from '@/components/employee/employee-filter-bar';
import { Button } from '@/components/ui/button';
import { CustomPagination } from '@/components/custom-pagination';
import { BranchesTableConfig } from '@/config/tables/branch-table';
import AppLayout from '@/layouts/hr-layout';
import { type BreadcrumbItem } from '@/types';
import { DeleteConfirmationDialog } from "@/components/delete-confirmation-modal";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Branches',
        href: '/hr/branches',
    },
];

interface Branches {
    id: number;
    branch_name: string;
    branch_slug: string;
    branch_address: string;
    sites?: {
        id: number;
        site_name: string;
    }[];
}

interface LinkProps {
    active: boolean;
    label: string;
    url: string | null;
}

interface BranchesPagination {
    data: Branches[];
    links: LinkProps[];
    from: number;
    to: number;
    total: number;
}

interface FilterProps {
    search: string;
    perPage: string;
}

interface IndexProps {
    branches: BranchesPagination;
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

export default function Index({ branches, filters, totalCount, filteredCount }: IndexProps) {
    const { delete: destroy } = useForm();
    const { props } = usePage<{ flash?: { success?: string; error?: string; warning?: string; info?: string } }>();

    // Delete confirmation dialog state
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [branchToDelete, setBranchToDelete] = useState<Branches | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // ── Clear filters loading state ──────────────────────────────────────────
    const [isClearingFilters, setIsClearingFilters] = useState(false);
    const isClearingRef = useRef(false);
    const clearFiltersTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
    const [lastSearchTerm, setLastSearchTerm] = useState('');

    const { data, setData } = useForm({
        search: filters.search || '',
        perPage: filters.perPage || '10',
    });

    // Track last shown flash to prevent duplicates within a short time window
    const lastFlashRef = useRef<{ key: string; time: number }>({ key: '', time: 0 });

    useEffect(() => {
        const flash = props.flash;
        if (!flash) return;

        const flashKey = JSON.stringify(flash);
        const now = Date.now();
        const last = lastFlashRef.current;

        if (last.key === flashKey && (now - last.time) < 500) {
            return;
        }

        lastFlashRef.current = { key: flashKey, time: now };

        if (flash.success) {
            toast.success(flash.success, toastStyle('#16a34a'));
        }
        if (flash.error) {
            toast.error(flash.error, toastStyle('#dc2626'));
        }
        if (flash.warning) {
            toast.warning(flash.warning, toastStyle('#f97316'));
        }
        if (flash.info) {
            toast.info(flash.info, toastStyle('#3b82f6'));
        }
    }, [props.flash]);

    const handleSearchChange = (value: string) => {
        if (isClearingRef.current) return;
        setData('search', value);
        
        const queryString = {
            ...(value && { search: value }),
            ...(data.perPage && { perPage: data.perPage }),
        };

        router.get(BranchController.index.url(), queryString, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleClearAll = () => {
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
        
        router.get(BranchController.index.url(), {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                clearFiltersTimer.current = setTimeout(() => {
                    isClearingRef.current = false;
                    setIsClearingFilters(false);
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

        const queryString = {
            ...(data.search && { search: data.search }),
            ...(value && { perPage: value }),
        };

        router.get(BranchController.index.url(), queryString, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleDeleteClick = (branch: Branches) => {
        setBranchToDelete(branch);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (!branchToDelete) return;

        setIsDeleting(true);
        destroy(BranchController.destroy(branchToDelete.branch_slug).url, {
            onSuccess: () => {
                setDeleteDialogOpen(false);
                setBranchToDelete(null);
            },
            onError: (errors) => {
                const errorMessage = Object.values(errors).flat()[0] || 'Failed to delete branch.';
                toast.error(errorMessage, toastStyle('#dc2626'));
            },
            onFinish: () => {
                setIsDeleting(false);
            }
        });
    };

    const editBranch = (branch: Branches) => {
        if (isClearingRef.current) return;
        router.get(BranchController.edit(branch.branch_slug).url);
    };

    const hasActiveFilters = !!data.search.trim();
    const showFilterEmptyState = hasActiveFilters || isClearingFilters || (lastSearchTerm && lastSearchTerm.trim() !== '');

    const getFilterDisplayText = () => {
        if (isClearingFilters && lastSearchTerm) {
            return `No branches matching "${lastSearchTerm}".`;
        }
        if (data.search) {
            return `No branches matching "${data.search}".`;
        }
        return 'No branches match your current filters.';
    };

    const handleShowSites = (branch: Branches) => {
        if (isClearingRef.current) return;
        router.get(BranchController.show(branch.branch_slug).url);
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Branches" />

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

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4 mx-4">
                <div className="flex pp-header justify-between">
                    <CustomHeader
                        title='Branch'
                        icon={<Building2 className="h-6 w-6" />}
                        description='A list of all branches'
                    />

					{branches.total >= 1 && (
                    <div className="flex items-center gap-2">
                        <Link
                            href={BranchController.create()}
                            className="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 whitespace-nowrap"
                        >
                            + Add Branch
                        </Link>
                    </div>
					)}
					
                </div>

                <div className="pp-row">
                    <CustomTable
                        columns={BranchesTableConfig.columns}
                        actions={BranchesTableConfig.actions}
                        data={branches.data}
                        from={branches.from}
                        onDelete={handleDeleteClick}
                        onView={handleShowSites}
                        onEdit={editBranch}
                        title="Branches"
                        hasActiveFilters={hasActiveFilters}
                        searchTerm={data.search}
                        toolbar={
                            <EmployeeFilterBar
                                filters={{
                                    search: true,
                                    position: false,
                                    branch: false,
                                    site: false,
                                    date: false,
                                    status: false,
                                }}
                                searchTerm={data.search}
                                onSearchChange={handleSearchChange}
                                onClearAll={handleClearAll}
                                searchPlaceholder="Search by branch name or address..."
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
                                        onClick={handleClearAll}
                                        disabled={isClearingFilters}
                                        className="rounded-xl border-2 border-border px-4 py-2 text-sm font-semibold"
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
                                        <Building2 className="h-5 w-5 text-slate-500 dark:text-slate-400" />
                                    </div>
                                    <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                        No branches yet.
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 mb-4 max-w-xs">
                                        Get started by creating your first branch.
                                    </p>
                                    <Link href={BranchController.create()}>
                                        <Button size="sm">
                                            + Add Branch
                                        </Button>
                                    </Link>
                                </div>
                            )
                        }
                    />

					{branches.total >= 1 && (
                    <CustomPagination
                        pagination={branches}
                        perPage={data.perPage}
                        onPerPageChange={handlePerPageChange}
                        totalCount={totalCount}
                        filteredCount={filteredCount}
                        search={data.search}
                        resourceName='branches'
                    />
					)}

                    <DeleteConfirmationDialog
                        isOpen={deleteDialogOpen}
                        onClose={() => {
                            setDeleteDialogOpen(false);
                            setBranchToDelete(null);
                        }}
                        onConfirm={confirmDelete}
                        title="Delete Branch"
                        itemName={branchToDelete?.branch_name}
                        isLoading={isDeleting}
                        confirmText="Delete Branch"
                    />
                </div>
            </div>
        </AppLayout>
    );
}