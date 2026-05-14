// config/tables/payroll-table-config.ts

export interface PayrollTableRow {
    id: number;
    employee_name: string;
    employee_avatar?: string | null;
    emp_code: string;
    branch_name: string;
    site_name: string;
    period_name: string;
    period_start: string;
    period_end: string;
    position_name: string;
    pay_frequency: string;
    gross_pay: number;
    total_deduction: number;
    net_pay: number;
    _original: any;
}

// Helper: format date to short month (e.g., "Jan 15, 2024")
const formatDateToShortMonth = (dateString: string): string => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'N/A';
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
    } catch {
        return 'N/A';
    }
};

// Helper: get avatar URL
const getAvatarUrl = (avatar: string | null | undefined): string | null => {
    if (!avatar) return null;
    if (avatar.startsWith('/storage/') || avatar.startsWith('http')) return avatar;
    return `/storage/${avatar}`;
};

export const getPayrollTableColumns = (formatCurrency: (amount: number) => string) => [
    // ── Always-visible columns (toggleable: false) ──────────────────────
    {
        label: 'Profile',
        key: 'employee_avatar',
        toggleable: false,
        render: (row: PayrollTableRow) => {
            const avatarUrl = getAvatarUrl(row.employee_avatar);
            return avatarUrl ? (
                <img
                    src={avatarUrl}
                    alt={row.employee_name}
                    className="w-10 h-10 rounded-full object-cover"
                    onError={(e) => {
                        const target = e.target as HTMLImageElement;
                        target.style.display = 'none';
                        const parent = target.parentElement;
                        if (parent) {
                            const fallback = document.createElement('div');
                            fallback.className = 'w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center';
                            fallback.innerHTML = `<span class="text-xs font-medium text-blue-600">${row.employee_name?.charAt(0).toUpperCase() || '?'}</span>`;
                            parent.appendChild(fallback);
                        }
                    }}
                />
            ) : (
                <div className="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                    <span className="text-xs font-medium text-blue-900">
                        {row.employee_name?.charAt(0).toUpperCase() || '?'}
                    </span>
                </div>
            );
        },
    },

    {
        label: 'ID',
        key: 'emp_code',
        toggleable: true,
        defaultVisible: false,
        render: (row: PayrollTableRow) => (
            <span className="text-xs text-gray-500 font-mono">{row.emp_code}</span>
        ),
    },
    {
        label: 'Name',
        key: 'employee_name',
        toggleable: false,
        render: (row: PayrollTableRow) => (
            <span className="font-medium text-sm">{row.employee_name}</span>
        ),
    },

    {
        label: 'Branch',
        key: 'branch_name',
        toggleable: true,
        defaultVisible: true,
        render: (row: PayrollTableRow) => (
            <span className="text-sm">{row.branch_name || 'N/A'}</span>
        ),
    },
    {
        label: 'Site',
        key: 'site_name',
        toggleable: true,
        defaultVisible: true,
        render: (row: PayrollTableRow) => (
            <span className="text-sm">{row.site_name || 'N/A'}</span>
        ),
    },
    {
        label: 'Position',
        key: 'position_name',
        toggleable: true,
        defaultVisible: false,
        render: (row: PayrollTableRow) => (
            <span className="text-sm">{row.position_name || 'N/A'}</span>
        ),
    },
    {
        label: 'Frequency',
        key: 'pay_frequency',
        toggleable: true,
        defaultVisible: false,
        render: (row: PayrollTableRow) => (
            <span className="text-sm capitalize">{row.pay_frequency?.replace('_', ' ') || 'N/A'}</span>
        ),
    },
    {
        label: 'Period',
        key: 'period_name',
        toggleable: true,
        defaultVisible: false,
        render: (row: PayrollTableRow) => (
            <div className="flex flex-col">
                <span className="text-sm font-medium">{row.period_name || 'N/A'}</span>
                {row.period_start && row.period_end && (
                    <span className="text-xs text-gray-500">
                        {formatDateToShortMonth(row.period_start)} – {formatDateToShortMonth(row.period_end)}
                    </span>
                )}
            </div>
        ),
    },
    {
        label: 'Gross Pay',
        key: 'gross_pay',
        toggleable: true,
        defaultVisible: true,
        render: (row: PayrollTableRow) => (
            <span className="font-medium text-green-600">{formatCurrency(row.gross_pay)}</span>
        ),
    },
    {
        label: 'Deductions',
        key: 'total_deduction',
        toggleable: true,
        defaultVisible: true,
        render: (row: PayrollTableRow) => (
            <span className="text-red-600">{formatCurrency(row.total_deduction)}</span>
        ),
    },
    {
        label: 'Net Pay',
        key: 'net_pay',
        toggleable: true,
        defaultVisible: true,
        render: (row: PayrollTableRow) => (
            <span className="font-bold text-blue-600">{formatCurrency(row.net_pay)}</span>
        ),
    },

    // ── Actions (never toggleable) ───────────────────────────────────────
    {
        label: '',
        key: 'actions',
        isAction: true,
        toggleable: false,
        className: 'p-4 text-center',
    },
];

export const getPayrollTableActions = (
    handleViewPayroll: (row: PayrollTableRow) => void,
    handleEmailPayroll: (row: PayrollTableRow) => void
) => [
        {
            label: 'View',
            icon: 'Eye',
            onClick: (row: PayrollTableRow) => handleViewPayroll(row),
        },
        {
            label: 'Email',
            icon: 'Mail',
            onClick: (row: PayrollTableRow) => handleEmailPayroll(row),
        },
    ];

export const getSkeletonColumns = () =>
    [
        'ID',
        'Employee',
        'Branch',
        'Site',
        'Position',
        'Frequency',
        'Period',
        'Gross Pay',
        'Deductions',
        'Net Pay',
        'Actions',
    ].map((label) => ({
        label,
        key: label.toLowerCase().replace(/\s+/g, '_'),
        className: '',
    }));