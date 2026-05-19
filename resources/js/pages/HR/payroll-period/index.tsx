import { Head, Link, usePage, router } from '@inertiajs/react';
import {
    CalendarDays, Plus, Clock, CheckCircle2,
    AlertCircle, Filter, Pencil, Trash2, Eye, Calendar, XCircle, Loader2,
    BadgeCheck, X, Search
} from 'lucide-react';
import { useState, useMemo, useEffect, useRef } from 'react';
import HRPayrollPeriodController from '@/actions/App/Http/Controllers/HrRole/PayrollPeriodController';
import { CustomHeader } from '@/components/custom-header';
import { CustomTable } from '@/components/custom-table';
import {
    Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription,
} from '@/components/ui/dialog';
import {
    Select, SelectContent, SelectItem, SelectTrigger, SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/hr-layout';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';

// ── Types ─────────────────────────────────────────────────────────────────────
interface PayrollPeriod {
    id: number;
    start_date: string;
    end_date: string;
    pay_date: string;
    payroll_per_status: string;
    is_paid: boolean;
    created_at?: string;
    updated_at?: string;
}

interface PayrollPeriodProps { payrollPeriods: PayrollPeriod[]; }
interface PageProps {
    payroll_period_enums: Array<{ value: string; label: string; }>;
    flash?: { success?: string; error?: string; warning?: string; info?: string };
    [key: string]: any;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Payroll Periods', href: '/hr/payroll-periods' },
];

// Custom toast style helper for sonner
const toastStyle = (color: string) => ({
    style: {
        backgroundColor: 'white',
        color: color,
        border: '1px solid #e2e8f0',
        boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)',
    },
});

// ── Status config based on enum values ─────────────────────────────────────────
const getStatusConfig = (status: string) => {
    const configs: Record<string, {
        icon: any;
        badge: string;
        dot: string;
        color: 'primary' | 'secondary' | 'accent' | 'muted'
    }> = {
        open: {
            icon: AlertCircle,
            badge: 'bg-slate-100 text-slate-700 border border-slate-200',
            dot: 'bg-slate-400',
            color: 'muted'
        },
        processing: {
            icon: Clock,
            badge: 'bg-amber-50 text-amber-700 border border-amber-200',
            dot: 'bg-amber-500',
            color: 'secondary'
        },
        calculated: {
            icon: CheckCircle2,
            badge: 'bg-sky-50 text-sky-700 border border-sky-200',
            dot: 'bg-sky-500',
            color: 'primary'
        },
        completed: {
            icon: BadgeCheck,
            badge: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            dot: 'bg-emerald-500',
            color: 'accent'
        },
        failed: {
            icon: AlertCircle,
            badge: 'bg-rose-50 text-rose-700 border border-rose-200',
            dot: 'bg-rose-500',
            color: 'muted'
        },
    };

    return configs[status] || configs.open;
};

function StatusBadge({ status, label, isProcessing, progress }: { status: string; label: string; isProcessing?: boolean; progress?: number }) {
    if (isProcessing) {
        return (
            <div className="inline-flex items-center gap-2 rounded-full px-2.5 py-1 bg-primary/10 border border-primary/20">
                <Loader2 className="h-3 w-3 animate-spin text-primary" />
                <span className="text-[10px] font-black uppercase tracking-wider text-primary">
                    {progress !== undefined && progress > 0 ? `${progress}%` : 'Processing'}
                </span>
            </div>
        );
    }

    const cfg = getStatusConfig(status);
    const Icon = cfg.icon;

    return (
        <span className={`inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wider ${cfg.badge}`}>
            <Icon className="h-3 w-3" color="#17ab30" />
            {label}
        </span>
    );
}

function PaymentBadge({ isPaid }: { isPaid: boolean }) {
    return isPaid ? (
        <span className="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-green-700">
            <CheckCircle2 className="h-3 w-3" />
            Paid
        </span>
    ) : (
        <span className="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-gray-600">
            <XCircle className="h-3 w-3" />
            Unpaid
        </span>
    );
}

// ── Stat card ───────────────────────────────────────────────────────────────
function StatCard({ label, count, active, onClick, color }: {
    label: string; count: number; active: boolean;
    onClick: () => void; color: 'primary' | 'secondary' | 'accent' | 'muted';
}) {
    const colorMap = {
        primary: { bg: 'bg-primary', text: 'text-primary-foreground', ring: 'ring-primary' },
        secondary: { bg: 'bg-secondary', text: 'text-secondary-foreground', ring: 'ring-secondary' },
        accent: { bg: 'bg-accent', text: 'text-accent-foreground', ring: 'ring-accent' },
        muted: { bg: 'bg-muted', text: 'text-muted-foreground', ring: 'ring-border' },
    };
    const c = colorMap[color];
    return (
        <button
            type="button"
            onClick={onClick}
            className={`flex flex-col gap-1 rounded-2xl p-5 text-left shadow-sm transition-all duration-200 ring-2
                ${active ? `${c.bg} ${c.text} ${c.ring} shadow-md scale-[1.02]` : 'bg-card text-foreground ring-border hover:ring-primary/40 hover:shadow-md'}`}
        >
            <p className={`text-[10px] font-black uppercase tracking-widest ${active ? 'opacity-70' : 'text-muted-foreground'}`}>
                {label}
            </p>
            <p className="text-3xl font-extrabold">{count}</p>
        </button>
    );
}

// ── Main component ──────────────────────────────────────────────────────────
export default function Index({ payrollPeriods }: PayrollPeriodProps) {
    const { payroll_period_enums, flash } = usePage<PageProps>().props;

    const [selectedPeriod, setSelectedPeriod] = useState<PayrollPeriod | null>(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [statusFilter, setStatusFilter] = useState<string>(() =>
        localStorage.getItem('payrollPeriods-statusFilter') || 'all'
    );
    
    // Search state
    const [searchTerm, setSearchTerm] = useState<string>('');

    // ── Clear filters loading state ──────────────────────────────────────────
    const [isClearingFilters, setIsClearingFilters] = useState(false);
    const isClearingRef = useRef(false);
    const clearFiltersTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
    
    // Store last filter values for display during clearing
    const [lastSearchTerm, setLastSearchTerm] = useState('');
    const [lastStatusFilter, setLastStatusFilter] = useState('');

    // Simplified processing state
    const [processingPeriodId, setProcessingPeriodId] = useState<number | null>(null);
    const [processingProgress, setProcessingProgress] = useState<number>(0);
    const [processingMessage, setProcessingMessage] = useState<string>('');

    // Track last shown flash to prevent duplicates within a short time window
    const lastFlashRef = useRef<{ key: string; time: number }>({ key: '', time: 0 });

    // Flash message listener – prevents duplicate toasts within 500ms
    useEffect(() => {
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
    }, [flash]);

    useEffect(() => {
        localStorage.setItem('payrollPeriods-statusFilter', statusFilter);
    }, [statusFilter]);

    // Listen to both payroll and payroll-period channels
    useEffect(() => {
        if (!window.Echo) {
            return;
        }

        const payrollChannel = window.Echo.private('payroll');
        const payrollPeriodChannel = window.Echo.private('payroll-period');

        const handlePayrollEvent = (event: any) => {
            if (event.progress !== undefined && event.payroll_period_id) {
                const isStillProcessing = event.progress < 100;

                setProcessingPeriodId(event.payroll_period_id);
                setProcessingProgress(event.progress);
                setProcessingMessage(event.message);

                if (!isStillProcessing) {
                    setTimeout(() => {
                        setProcessingPeriodId(null);
                        setProcessingProgress(0);
                        setProcessingMessage('');
                    }, 3000);
                }
            }

            router.reload({ only: ['payrollPeriods'] });
        };

        payrollChannel.listen('.payroll.completed', handlePayrollEvent);
        payrollPeriodChannel.listen('.payroll.completed', handlePayrollEvent);

        payrollChannel.subscribed(() => {});
        payrollPeriodChannel.subscribed(() => {});

        payrollChannel.error((error: any) => {});
        payrollPeriodChannel.error((error: any) => {});

        return () => {
            payrollChannel.stopListening('.payroll.completed');
            payrollPeriodChannel.stopListening('.payroll.completed');
        };
    }, []);

    const formatDate = (d: string) => new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

    const formatStatus = (status: string) => {
        const found = payroll_period_enums?.find((e) => e.value.toLowerCase() === status.toLowerCase());
        return found?.label || status.charAt(0).toUpperCase() + status.slice(1);
    };

    // Filter by status AND search term
    const filteredPeriods = useMemo(() => {
        let filtered = payrollPeriods;
        
        // Apply status filter
        if (statusFilter !== 'all') {
            filtered = filtered.filter((p) => p.payroll_per_status === statusFilter);
        }
        
        // Apply search filter (search by period range)
        if (searchTerm) {
            const term = searchTerm.toLowerCase();
            filtered = filtered.filter((p) => {
                const startDate = formatDate(p.start_date).toLowerCase();
                const endDate = formatDate(p.end_date).toLowerCase();
                const payDate = formatDate(p.pay_date).toLowerCase();
                const status = formatStatus(p.payroll_per_status).toLowerCase();
                
                return startDate.includes(term) || 
                       endDate.includes(term) || 
                       payDate.includes(term) ||
                       status.includes(term);
            });
        }
        
        return filtered;
    }, [payrollPeriods, statusFilter, searchTerm]);

    // Generate counts based on enum values
    const counts = useMemo(() => {
        const countsMap: Record<string, number> = {
            all: payrollPeriods.length,
        };

        payroll_period_enums?.forEach((enumItem) => {
            countsMap[enumItem.value] = payrollPeriods.filter(
                (p) => p.payroll_per_status === enumItem.value
            ).length;
        });

        return countsMap;
    }, [payrollPeriods, payroll_period_enums]);

    // ── Column definitions for CustomTable ─────────────────────────────────
    const columns = [
        {
            label: 'Period',
            key: 'start_date',
            render: (row: PayrollPeriod) => (
                <div className="flex items-center gap-2">
                    <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                        <CalendarDays className="h-4 w-4 text-primary" />
                    </div>
                    <div>
                        <p className="text-sm font-semibold text-foreground">{formatDate(row.start_date)}</p>
                        <p className="text-xs text-muted-foreground">to {formatDate(row.end_date)}</p>
                    </div>
                </div>
            ),
        },
        {
            label: 'Pay Date',
            key: 'pay_date',
            render: (row: PayrollPeriod) => (
                <span className="text-sm text-foreground">{formatDate(row.pay_date)}</span>
            ),
        },
        {
            label: 'Status',
            key: 'payroll_per_status',
            render: (row: PayrollPeriod) => {
                const isProcessing = processingPeriodId === row.id;
                return (
                    <StatusBadge
                        status={row.payroll_per_status}
                        label={formatStatus(row.payroll_per_status)}
                        isProcessing={isProcessing}
                        progress={processingProgress}
                    />
                );
            },
        },
        {
            label: 'Payment',
            key: 'is_paid',
            render: (row: PayrollPeriod) => (
                <PaymentBadge isPaid={row.is_paid} />
            ),
        },
        {
            label: 'Actions',
            key: 'actions',
            isAction: true,
        },
    ];

    // Actions – View, Edit, and Run Payroll
    const actions = [
        { label: 'View', icon: 'Eye' as const },
        { label: 'Edit', icon: 'Pencil' as const },
        { label: 'Run Payroll', icon: 'Play' as const },
    ];

    const handleRunPayroll = (period: PayrollPeriod) => {
        if (period.payroll_per_status !== 'open') return;

        router.put(
            `/hr/payroll-periods/${period.id}`,
            {
                start_date: period.start_date,
                end_date: period.end_date,
                pay_date: period.pay_date,
                payroll_per_status: 'processing',
                is_paid: period.is_paid,
            },
            {
                preserveScroll: true,
                onSuccess: () => toast.success('Payroll run started.', toastStyle('#16a34a')),
                onError: () => toast.error('Failed to start payroll run.', toastStyle('#dc2626')),
            }
        );
    };

    // Handle search
    const handleSearchChange = (value: string) => {
        if (isClearingRef.current) return;
        setSearchTerm(value);
    };

    const handleStatusFilterChange = (value: string) => {
        if (isClearingRef.current) return;
        setStatusFilter(value);
    };

    const clearAllFilters = () => {
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
        setStatusFilter('all');
        setSearchTerm('');
        
        // Navigate with cleared filters
        router.get('/hr/payroll-periods', {}, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => {
                clearFiltersTimer.current = setTimeout(() => {
                    isClearingRef.current = false;
                    setIsClearingFilters(false);
                    // Clear stored filters after a delay
                    setTimeout(() => {
                        setLastSearchTerm('');
                        setLastStatusFilter('');
                    });
                });
            }
        });
    };

    // Determine which empty state to show
    const hasActiveFilters = statusFilter !== 'all' || !!searchTerm;
    const showFilterEmptyState = hasActiveFilters || isClearingFilters || (lastSearchTerm && lastSearchTerm.trim() !== '') || (lastStatusFilter && lastStatusFilter !== 'all');

    // Helper to format filter display text
    const getFilterDisplayText = () => {
        if (isClearingFilters && lastSearchTerm && lastStatusFilter !== 'all') {
            const statusLabel = formatStatus(lastStatusFilter);
            return `No payroll periods matching "${lastSearchTerm}" with status "${statusLabel}".`;
        }
        if (isClearingFilters && lastSearchTerm) {
            return `No payroll periods matching "${lastSearchTerm}".`;
        }
        if (isClearingFilters && lastStatusFilter !== 'all') {
            const statusLabel = formatStatus(lastStatusFilter);
            return `No payroll periods with status "${statusLabel}".`;
        }
        if (searchTerm && statusFilter !== 'all') {
            const statusLabel = formatStatus(statusFilter);
            return `No payroll periods matching "${searchTerm}" with status "${statusLabel}".`;
        }
        if (searchTerm) {
            return `No payroll periods matching "${searchTerm}".`;
        }
        if (statusFilter !== 'all') {
            const statusLabel = formatStatus(statusFilter);
            return `No payroll periods with status "${statusLabel}".`;
        }
        return 'No payroll periods match your current filters.';
    };

    // Toolbar slot for the filter controls with search
    const toolbar = (
        <div className="flex flex-wrap items-center justify-between gap-3">
            {processingPeriodId !== null && (
                <div className="flex items-center gap-2 rounded-lg bg-primary/10 px-3 py-2">
                    <Loader2 className="h-4 w-4 animate-spin text-primary" />
                    <span className="text-sm font-medium text-primary">
                        {processingMessage || 'Processing payroll...'}
                    </span>
                    {processingProgress > 0 && (
                        <span className="text-xs font-bold text-primary">
                            {processingProgress}%
                        </span>
                    )}
                </div>
            )}
            
            <p className="text-sm text-muted-foreground">
                {statusFilter === 'all' && !searchTerm
                    ? `Showing all ${filteredPeriods.length} periods`
                    : (statusFilter !== 'all' || searchTerm) && (
                        <>Filtered by {statusFilter !== 'all' && <span className="font-semibold text-foreground">{formatStatus(statusFilter)}</span>}
                        {searchTerm && <span className="font-semibold text-foreground"> "{searchTerm}"</span>} — {filteredPeriods.length} result{filteredPeriods.length !== 1 ? 's' : ''}</>
                    )
                }
            </p>
            
            <div className="flex items-center gap-2">
                <Filter className="h-4 w-4 text-muted-foreground" />
                <Select value={statusFilter} onValueChange={handleStatusFilterChange} disabled={isClearingFilters}>
                    <SelectTrigger className="h-9 w-[180px] rounded-xl border-2 text-sm focus:border-primary">
                        <SelectValue placeholder="Filter by status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Statuses</SelectItem>
                        {payroll_period_enums?.map(({ value, label }) => (
                            <SelectItem key={value} value={value}>{label}</SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                {hasActiveFilters && (
                    <button
                        onClick={clearAllFilters}
                        disabled={isClearingFilters}
                        className="rounded-xl border-2 border-border px-3 py-1.5 text-xs font-semibold text-muted-foreground transition-all hover:border-accent hover:text-accent active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {isClearingFilters ? 'Clearing...' : 'Clear All'}
                    </button>
                )}
            </div>
        </div>
    );
    
    // Get color for stat card based on status
    const getStatusColor = (status: string): 'primary' | 'secondary' | 'accent' | 'muted' => {
        const config = getStatusConfig(status);
        return config.color;
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Payroll Periods" />

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

            <div className="py-4 min-h-[calc(95vh-6.5rem)] -mt-5 md:py-6">
                <div className="mx-auto px-4 sm:px-6 lg:px-8">

                    {/* ── Page header ── */}
                    <div className="grid grid-rows-1 justify-center sm:mx-1 md:grid-row-1 md:mx-0 mt-3 lg:flex lg:justify-between items-center lg:mx-0 lg:mt-3 lg:pb-4 lg:-mb-2 pp-header">
                        <CustomHeader
                            icon={<Calendar className="h-6 w-6" />}
                            title="Payroll Periods"
                            description="Manage and organize payroll periods with ease."
                        />

                        <div className="flex flex-row justify-between items-center gap-3 -mt-2 mb-3 lg:flex-col lg:items-end lg:justify-end lg:gap-2 lg:-mt-5">
                            <div className="flex items-center gap-2 lg:order-1 mt-2">
                                <span className='border px-1.5 py-0.1 rounded-full text-sm bg-primary/10 border-primary/30'>
                                    {payrollPeriods.length}
                                </span>
                                <span className='text-sm font-medium whitespace-nowrap'>
                                    total <span className="text-blue-800 font-bold">{payrollPeriods.length === 1 ? 'period' : 'periods'}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* ── Stat filter cards (only show if there are periods) ── */}
                    {payrollPeriods.length > 0 && (
                        <div className="mb-4 mx-1 md:mb-4 lg:mb-8 grid grid-cols-2 md:grid-cols-2 gap-3 lg:grid-cols-4 pp-header">
                            <StatCard
                                label="All"
                                count={counts.all}
                                active={statusFilter === 'all' && !searchTerm}
                                onClick={() => {
                                    if (!isClearingFilters) {
                                        setStatusFilter('all');
                                        setSearchTerm('');
                                    }
                                }}
                                color="muted"
                            />
                            {payroll_period_enums?.map(({ value, label }) => (
                                <StatCard
                                    key={value}
                                    label={label}
                                    count={counts[value] || 0}
                                    active={statusFilter === value && !searchTerm}
                                    onClick={() => {
                                        if (!isClearingFilters) {
                                            setStatusFilter(value);
                                            setSearchTerm('');
                                        }
                                    }}
                                    color={getStatusColor(value)}
                                />
                            ))}
                        </div>
                    )}
                    
                    <div className="pp-row">
                        <CustomTable
                            title="Payroll Period Lists"
                            columns={columns}
                            actions={actions}
                            data={filteredPeriods}
                            from={1}
                            onView={(period) => { setSelectedPeriod(period); setIsModalOpen(true); }}
                            onEdit={(period) => router.visit(HRPayrollPeriodController.edit(period.id).url)}
                            onRunPayroll={handleRunPayroll}
                            toolbar={toolbar}
                            searchTerm={searchTerm}
                            hasActiveFilters={hasActiveFilters}
                            emptyState={
                                showFilterEmptyState ? (
                                    <div className="flex flex-col items-center justify-center rounded-2xl py-16 text-center">
                                        <div className="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10">
                                            <Search className="h-6 w-6 text-primary/50" />
                                        </div>
                                        <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                            No results found
                                        </h3>
                                        <p className="text-xs text-slate-500 dark:text-slate-400 mb-4 max-w-xs">
                                            {getFilterDisplayText()}
                                        </p>
                                        <button
                                            onClick={clearAllFilters}
                                            disabled={isClearingFilters}
                                            className="rounded-xl border-2 border-border px-4 py-2 text-sm font-semibold text-foreground transition-all hover:border-primary hover:text-primary active:scale-95 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            {isClearingFilters ? 'Clearing filters...' : 'Clear filters'}
                                        </button>
                                    </div>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-12 px-6 text-center">
                                        <div className="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                            <Calendar className="h-5 w-5 text-slate-500 dark:text-slate-400" />
                                        </div>
                                        <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">No payroll periods yet.</h3>
                                        <p className="text-xs text-slate-500 dark:text-slate-400 mb-4 max-w-xs">
                                            Please import your attendance to automatically create payroll periods.
                                        </p>
                                    </div>
                                )
                            }
                        />
                    </div>

                    {/* ── Detail modal view ── */}
                    <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
                        <DialogContent className="sm:max-w-[480px] rounded-2xl">
                            <DialogHeader>
                                <DialogTitle className="flex items-center gap-2 text-base font-extrabold">
                                    <div className="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                                        <CalendarDays className="h-4 w-4 text-primary" />
                                    </div>
                                    Payroll Period Details
                                </DialogTitle>
                                <DialogDescription className="text-xs text-muted-foreground">
                                    Full details for this payroll period.
                                </DialogDescription>
                            </DialogHeader>

                            {selectedPeriod && (
                                <div className="space-y-3 pt-2">
                                    <div className="rounded-xl border border-border bg-card p-4">
                                        <p className="mb-3 text-[10px] font-black uppercase tracking-widest text-muted-foreground">
                                            Period Range
                                        </p>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <p className="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Start</p>
                                                <p className="text-sm font-semibold text-foreground">{formatDate(selectedPeriod.start_date)}</p>
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-black uppercase tracking-widest text-muted-foreground">End</p>
                                                <p className="text-sm font-semibold text-foreground">{formatDate(selectedPeriod.end_date)}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="rounded-xl border border-border bg-card p-4">
                                        <p className="mb-1 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Pay Date</p>
                                        <p className="text-sm font-semibold text-foreground">{formatDate(selectedPeriod.pay_date)}</p>
                                    </div>

                                    <div className="rounded-xl border border-border bg-card p-4">
                                        <p className="mb-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Status</p>
                                        <StatusBadge
                                            status={selectedPeriod.payroll_per_status}
                                            label={formatStatus(selectedPeriod.payroll_per_status)}
                                            isProcessing={processingPeriodId === selectedPeriod.id}
                                            progress={processingProgress}
                                        />
                                    </div>

                                    <div className="rounded-xl border border-border bg-card p-4">
                                        <p className="mb-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Payment Status</p>
                                        <PaymentBadge isPaid={selectedPeriod.is_paid} />
                                    </div>

                                    <div className="rounded-xl bg-primary/5 p-4">
                                        <p className="mb-1 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Summary</p>
                                        <p className="text-sm text-foreground">
                                            {processingPeriodId === selectedPeriod.id
                                                ? processingMessage || 'Processing payroll...'
                                                : selectedPeriod.payroll_per_status === 'completed' || selectedPeriod.payroll_per_status === 'calculated'
                                                    ? selectedPeriod.is_paid
                                                        ? 'This payroll period has been completed and the payout has been processed.'
                                                        : 'This payroll period has been completed but payout has not been confirmed.'
                                                    : selectedPeriod.payroll_per_status === 'processing'
                                                        ? 'This payroll period is currently being processed.'
                                                        : selectedPeriod.payroll_per_status === 'failed'
                                                            ? 'This payroll period failed to process. Please check logs for details.'
                                                            : 'This payroll period is open and pending processing.'}
                                        </p>
                                    </div>

                                    <div className="flex gap-2 pt-1">
                                        <Link
                                            href={HRPayrollPeriodController.edit(selectedPeriod.id).url}
                                            className="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-primary py-2.5 text-sm font-bold text-primary-foreground transition-all hover:brightness-110 active:scale-95"
                                        >
                                            <Pencil className="h-4 w-4" />
                                            Edit Period
                                        </Link>
                                        <button
                                            onClick={() => setIsModalOpen(false)}
                                            className="flex-1 rounded-xl border-2 border-border py-2.5 text-sm font-semibold text-foreground transition-all hover:border-primary hover:text-primary active:scale-95"
                                        >
                                            Close
                                        </button>
                                    </div>
                                </div>
                            )}
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
        </AppLayout>
    );
}