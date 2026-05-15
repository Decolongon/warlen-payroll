import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import Echo from 'laravel-echo';
import { CalendarDays, PlusCircle, CalendarClock, X, Bell, Eye, Pencil, Trash2, Search, Loader2 } from 'lucide-react';
import Pusher from 'pusher-js';
import { useState, useMemo, useEffect, useRef } from 'react';
import ApplicationLeaveController from "@/actions/App/Http/Controllers/HrRole/HRApplicationLeaveController";
import { CustomHeader } from '@/components/custom-header';
import { CustomPagination } from '@/components/custom-pagination';
import { CustomTable } from '@/components/custom-table';
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

// Import Echo and Pusher for Reverb
import { ApplicationLeavesTableConfig } from '@/config/tables/application-leave';
import AppLayout from '@/layouts/hr-layout';
import { type BreadcrumbItem, type BranchWithSites } from '@/types';

// Declare global window interface for Echo
declare global {
    interface Window {
        Echo: any;
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Application Leave',
        href: '/hr/application-leave',
    },
];

interface ApplicationLeaveProps {
    applicationLeaves: any[];
}

interface PageProps {
    applicationLeaveEnum: Array<{
        value: string;
        label: string;
    }>;
}

// Helper function to format dates
const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

function useDebounce<T>(value: T, delay: number): T {
    const [debouncedValue, setDebouncedValue] = useState<T>(value);
    useEffect(() => {
        const handler = setTimeout(() => setDebouncedValue(value), delay);
        return () => clearTimeout(handler);
    }, [value, delay]);
    return debouncedValue;
}

export default function Index({ applicationLeaves }: ApplicationLeaveProps) {
    const { delete: destroy } = useForm();
    const { applicationLeaveEnum } = usePage<PageProps>().props;

    // ── Clear filters loading state ──────────────────────────────────────────
    const [isClearingFilters, setIsClearingFilters] = useState(false);
    const isClearingRef = useRef(false);
    const clearFiltersTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
    const [lastSearchTerm, setLastSearchTerm] = useState('');
    const [lastStatusFilter, setLastStatusFilter] = useState('');

    // State for real-time updates
    const [leaves, setLeaves] = useState(applicationLeaves);
    const [notification, setNotification] = useState<{ message: string, timestamp: string } | null>(null);
    const [showNotification, setShowNotification] = useState(false);

    // Filter state
    const [statusFilter, setStatusFilter] = useState<string>(() => {
        const savedFilter = localStorage.getItem('applicationLeaves-statusFilter');
        return savedFilter || 'all';
    });

    // Search state
    const [searchTerm, setSearchTerm] = useState<string>("");

    // Dialog state for viewing details
    const [selectedLeave, setSelectedLeave] = useState<any>(null);
    const [isDialogOpen, setIsDialogOpen] = useState(false);

    // Listen to application-leave channel (Echo is already initialized globally)
    useEffect(() => {
        if (!window.Echo) return;

        const channel = window.Echo.private('application-leave');

        channel.listen('.ApplicationLeaveEvent', (event: any) => {
            setNotification({
                message: `New application leave created/updated`,
                timestamp: new Date().toLocaleString()
            });
            setShowNotification(true);

            // Auto-hide notification after 5 seconds
            setTimeout(() => {
                setShowNotification(false);
            }, 5000);

            // Update the leaves state with the new data
            setLeaves(prevLeaves => {
                const existingIndex = prevLeaves.findIndex(
                    leave => leave.id === event.id
                );

                if (existingIndex !== -1) {
                    const updatedLeaves = [...prevLeaves];
                    updatedLeaves[existingIndex] = {
                        ...updatedLeaves[existingIndex],
                        ...event,
                        employee: event.employee || updatedLeaves[existingIndex].employee
                    };
                    return updatedLeaves;
                } else {
                    return [event, ...prevLeaves];
                }
            });
        });

        channel.error((error: any) => {
            console.error('Channel error:', error);
        });

        return () => {
            channel.stopListening('.ApplicationLeaveEvent');
        };
    }, []);

    // Save filter to localStorage
    useEffect(() => {
        localStorage.setItem('applicationLeaves-statusFilter', statusFilter);
    }, [statusFilter]);

    // ── Filter handlers with clearing check ───────────────────────────────────
    const handleStatusChange = (value: string) => {
        if (isClearingRef.current) return;
        setStatusFilter(value);
    };

    const handleSearchChange = (value: string) => {
        if (isClearingRef.current) return;
        setSearchTerm(value);
    };

    // ── Clear filters function with timeout ───────────────────────────────────
    const clearFilters = () => {
        // Store current filter values before clearing
        setLastSearchTerm(searchTerm);
        setLastStatusFilter(statusFilter);
        
        // Clear any pending timers
        if (clearFiltersTimer.current) {
            clearTimeout(clearFiltersTimer.current);
        }
        
        // Set clearing flag
        isClearingRef.current = true;
        setIsClearingFilters(true);
        
        // Reset all filter states immediately
        setSearchTerm('');
        setStatusFilter('all');
        
        // Reset filters after timeout
        clearFiltersTimer.current = setTimeout(() => {
            isClearingRef.current = false;
            setIsClearingFilters(false);
            // Clear stored filters after a delay
            setTimeout(() => {
                setLastSearchTerm('');
                setLastStatusFilter('');
            }, 1000);
        }, 500);
    };

    // Handle delete
    const handleDelete = (slug_app: string) => {
        if (confirm("Are you sure you want to delete this application leave?")) {
            destroy(ApplicationLeaveController.destroy(slug_app).url, {
                onSuccess: () => {
                    setLeaves(prevLeaves =>
                        prevLeaves.filter(leave => leave.slug_app !== slug_app)
                    );
                }
            });
        }
    };

    // Handle view details
    const handleView = (leave: any) => {
        setSelectedLeave(leave);
        setIsDialogOpen(true);
    };

    // Handle edit
    const handleEdit = (row: any) => {
        if (isClearingRef.current) return;
        router.get(ApplicationLeaveController.edit(row.slug_app).url);
    };

    // Filter and search leaves
    const filteredLeaves = useMemo(() => {
        let result = leaves;

        // Filter by status
        if (statusFilter !== 'all') {
            result = result.filter(leave => {
                const status = leave.app_status || 'pending';
                return status.toLowerCase() === statusFilter.toLowerCase();
            });
        }

        // Filter by search term
        if (searchTerm.trim()) {
            const searchLower = searchTerm.toLowerCase().trim();
            result = result.filter(leave => {
                const employeeName = leave.employee?.user?.name || leave.employee_name || '';
                const employeeCode = leave.employee?.emp_code || '';
                return employeeName.toLowerCase().includes(searchLower) ||
                    employeeCode.toLowerCase().includes(searchLower);
            });
        }

        return result;
    }, [leaves, statusFilter, searchTerm]);

    // ── Empty state logic ─────────────────────────────────────────────────────
    const hasActiveFilters = !!(searchTerm || (statusFilter && statusFilter !== 'all'));
    const showFilterEmptyState = hasActiveFilters || isClearingFilters || 
                                (lastSearchTerm && lastSearchTerm.trim() !== '') || 
                                (lastStatusFilter && lastStatusFilter !== 'all');

    const getFilterDisplayText = () => {
        if (isClearingFilters && lastSearchTerm) {
            return `No leave applications matching "${lastSearchTerm}".`;
        }
        if (isClearingFilters && lastStatusFilter && lastStatusFilter !== 'all') {
            const statusLabel = applicationLeaveEnum?.find(s => s.value === lastStatusFilter)?.label || lastStatusFilter;
            return `No leave applications with status "${statusLabel}".`;
        }
        if (searchTerm && statusFilter !== 'all') {
            const statusLabel = applicationLeaveEnum?.find(s => s.value === statusFilter)?.label || statusFilter;
            return `No leave applications matching "${searchTerm}" with status "${statusLabel}".`;
        }
        if (searchTerm) {
            return `No leave applications matching "${searchTerm}".`;
        }
        if (statusFilter !== 'all') {
            const statusLabel = applicationLeaveEnum?.find(s => s.value === statusFilter)?.label || statusFilter;
            return `No leave applications with status "${statusLabel}".`;
        }
        return 'No leave applications match your current filters.';
    };

    // Get status badge color
    const getStatusBadgeClass = (status: string) => {
        const statusLower = status?.toLowerCase() || 'pending';
        switch (statusLower) {
            case 'approved':
                return 'bg-green-100 text-green-800';
            case 'rejected':
                return 'bg-red-100 text-red-800';
            case 'pending':
            default:
                return 'bg-yellow-100 text-yellow-800';
        }
    };

    // Format status text
    const formatStatus = (status: string) => {
        if (!status) return 'Pending';
        const found = applicationLeaveEnum?.find(item => item.value.toLowerCase() === status.toLowerCase());
        return found?.label || status.charAt(0).toUpperCase() + status.slice(1).toLowerCase();
    };

    // Define columns for CustomTable
    const columns = [
        {
            label: 'EMPLOYEE',
            key: 'employee_name',
            render: (row: any) => (
                <div className="flex flex-col">
                    <span className="font-medium text-sm">{row.employee?.user?.name || row.employee_name || 'N/A'}</span>
                    <span className="text-xs text-gray-500">ID: {row.employee?.emp_code || 'N/A'}</span>
                </div>
            )
        },
        {
            label: 'LEAVE START',
            key: 'leave_start',
            render: (row: any) => (
                <span className="text-sm">{formatDate(row.leave_start)}</span>
            )
        },
        {
            label: 'LEAVE END',
            key: 'leave_end',
            render: (row: any) => (
                <span className="text-sm">{formatDate(row.leave_end)}</span>
            )
        },
        {
            label: 'STATUS',
            key: 'app_status',
            isBadge: true,
            render: (row: any) => (
                <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${getStatusBadgeClass(row.app_status)}`}>
                    {formatStatus(row.app_status)}
                </span>
            )
        },
        {
            label: 'ACTIONS',
            key: 'actions',
            isAction: true,
        }
    ];

    // Define actions for CustomTable
    const actions = [
        { label: 'View', icon: 'Eye', route: '', className: '' },
        { label: 'Edit', icon: 'Pencil', route: '', className: '' },
        { label: 'Delete', icon: 'Trash2', route: '', className: 'text-red-600' }
    ];

    // ── Filter toolbar component with clear button ────────────────────────────
    const FilterToolbar = () => (
        <div className="flex flex-wrap items-center justify-between gap-4">
            {/* Search Input */}
            <div className="relative flex-1 min-w-[200px]">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    placeholder="Search by employee name or code..."
                    value={searchTerm}
                    onChange={(e) => handleSearchChange(e.target.value)}
                    className="pl-9 h-10 w-full rounded-xl border-2"
                    disabled={isClearingFilters}
                />
                {searchTerm && !isClearingFilters && (
                    <button
                        onClick={() => handleSearchChange('')}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                    >
                        <X className="h-4 w-4" />
                    </button>
                )}
            </div>

            {/* Status Filter */}
            <div className="w-[180px]">
                <Select 
                    value={statusFilter} 
                    onValueChange={handleStatusChange}
                    disabled={isClearingFilters}
                >
                    <SelectTrigger className="h-10 rounded-xl border-2">
                        <SelectValue placeholder="Filter by status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Statuses</SelectItem>
                        {applicationLeaveEnum?.map(({ value, label }) => (
                            <SelectItem key={value} value={value}>
                                {label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            {/* Clear All Button */}
            {hasActiveFilters && (
                <Button
                    variant="outline"
                    size="sm"
                    onClick={clearFilters}
                    disabled={isClearingFilters}
                    className="rounded-xl border-2"
                >
                    {isClearingFilters ? (
                        <>
                            <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                            Clearing...
                        </>
                    ) : (
                        'Clear All'
                    )}
                </Button>
            )}
        </div>
    );

    // ── Filter Empty State Component ──────────────────────────────────────────
    const FilterEmptyState = () => (
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
                onClick={clearFilters}
                disabled={isClearingFilters}
                className="rounded-xl border-2 border-border px-4 py-2 text-sm font-semibold"
            >
                {isClearingFilters ? 'Clearing filters...' : 'Clear filters'}
            </Button>
        </div>
    );

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Application Leaves" />

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

            {/* Page Header */}
            <div className="flex justify-between items-center p-4 mx-4 mt-2 -mb-6 pp-header">
                <CustomHeader
                    title="Application Leaves"
                    description="List of all application leaves"
                    icon={<CalendarClock className="h-6 w-6" />}
                />
            </div>

            <div className="@container/main flex flex-1 flex-col gap-2 p-4">
                {/* Notification Toast */}
                {showNotification && notification && (
                    <div className="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 animate-slide-in">
                        <Bell className="h-5 w-5 text-green-600" />
                        <div>
                            <p className="font-medium">{notification.message}</p>
                            <p className="text-xs text-green-600">{notification.timestamp}</p>
                        </div>
                        <Button
                            variant="ghost"
                            size="sm"
                            className="ml-4"
                            onClick={() => setShowNotification(false)}
                        >
                            <X className="h-4 w-4" />
                        </Button>
                    </div>
                )}

                {/* Table Section */}
                <div className='mx-4 pp-row'>
                    <CustomTable
                        title="Application Leave Lists"
                        columns={columns}
                        actions={actions}
                        data={filteredLeaves}
                        from={1}
                        onDelete={(id) => handleDelete(id as string)}
                        onView={handleView}
                        onEdit={handleEdit}
                        toolbar={<FilterToolbar />}
                        searchTerm={searchTerm}
                        hasActiveFilters={hasActiveFilters}
                        emptyState={
                            showFilterEmptyState ? (
                                <FilterEmptyState />
                            ) : (
                                <div className="flex flex-col items-center justify-center py-12 px-6 text-center">
                                    <div className="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                        <CalendarDays className="h-5 w-5 text-slate-500 dark:text-slate-400" />
                                    </div>
                                    <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                        No application leaves requested
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 mb-4 max-w-xs">
                                        There are no leave applications requested at the moment.
                                    </p>
                                </div>
                            )
                        }
                    />
                </div>
            </div>

            {/* Leave Details Dialog */}
            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                <DialogContent className="max-w-2xl max-h-[90vh] overflow-auto">
                    <DialogHeader>
                        <DialogTitle>Leave Application Details</DialogTitle>
                        <DialogDescription>Complete information about this leave application</DialogDescription>
                    </DialogHeader>
                    {selectedLeave && (
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-gray-500">Employee Name</label>
                                    <p className="font-semibold">{selectedLeave.employee?.user?.name || selectedLeave.employee_name || 'N/A'}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-500">Employee Code</label>
                                    <p>{selectedLeave.employee?.emp_code || 'N/A'}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-500">Leave Start</label>
                                    <p>{formatDate(selectedLeave.leave_start)}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-500">Leave End</label>
                                    <p>{formatDate(selectedLeave.leave_end)}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-500">Status</label>
                                    <p>
                                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(selectedLeave.app_status)}`}>
                                            {formatStatus(selectedLeave.app_status)}
                                        </span>
                                    </p>
                                </div>
                                {selectedLeave.leave_reason && (
                                    <div className="col-span-2">
                                        <label className="text-sm font-medium text-gray-500">Reason</label>
                                        <p className="mt-1 p-3 bg-gray-50 rounded-lg">{selectedLeave.leave_reason}</p>
                                    </div>
                                )}
                                {selectedLeave.remarks && (
                                    <div className="col-span-2">
                                        <label className="text-sm font-medium text-gray-500">Remarks</label>
                                        <p className="mt-1 p-3 bg-gray-50 rounded-lg">{selectedLeave.remarks}</p>
                                    </div>
                                )}
                            </div>
                            <div className="flex justify-end gap-2 pt-4">
                                <Button variant="outline" onClick={() => setIsDialogOpen(false)}>
                                    Close
                                </Button>
                                <Link href={ApplicationLeaveController.edit(selectedLeave.slug_app).url}>
                                    <Button>
                                        <Pencil className="h-4 w-4 mr-2" />
                                        Edit
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    )}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}