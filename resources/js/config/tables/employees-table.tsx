export const EmployeesTableConfig = {
  columns: [
    {
      label: 'Profile',
      key: 'avatar',
      className: 'p-4 align-items-center',
      toggleable: false,
      render: (row: any) => {
        if (!row.avatar) {
          return (
            <div className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          );
        }
        const avatarUrl = row.avatar.startsWith('http')
          ? row.avatar
          : `/storage/${row.avatar}`;
        return (
          <img
            src={avatarUrl}
            alt="Avatar"
            className="w-10 h-10 rounded-full object-cover border border-slate-200"
          />
        );
      }
    },
    {
      label: 'ID',
      key: 'emp_code',
      className: 'px-4 py-3 tracking-wider',
      toggleable: true,
      defaultVisible: false,
    },
    {
      label: 'Name',
      key: 'user.name',
      className: 'p-4',
      toggleable: false,
      render: (row: any) => row.user?.name || 'N/A'
    },
    {
      label: 'Position',
      key: 'position.pos_name',
      className: 'p-4',
      toggleable: true,
      defaultVisible: true,
      render: (row: any) => row.position?.pos_name && !row.position.deleted_at
        ? row.position.pos_name
        : <span className="text-gray-500 italic">Not assigned</span>
    },
    {
      label: 'Pay Frequency',
      key: 'pay_frequency',
      className: 'p-4 capitalize',
      toggleable: true,
      defaultVisible: false,
      render: (row: any) => row.pay_frequency?.replace('_', ' ') || 'N/A'
    },
    {
      label: 'Branch',
      key: 'branch.branch_name',
      className: 'p-4',
      toggleable: true,
      defaultVisible: true,
      render: (row: any) => row.branch?.branch_name || 'N/A'
    },
    {
      label: 'Site',
      key: 'site.site_name',
      className: 'p-4',
      toggleable: true,
      defaultVisible: true,
      render: (row: any) => row.site?.site_name || 'N/A'
    },
    {
      label: 'Contract Period',
      key: 'contract_period',
      className: 'p-4',
      toggleable: true,
      defaultVisible: false,
      render: (row: any) => {
        if (!row.contract_start_date) return 'No contract period';
        const start = formatDate(row.contract_start_date);
        const end = row.contract_end_date ? formatDate(row.contract_end_date) : 'Ongoing';
        return `${start} – ${end}`;
      }
    },
    {
      label: 'Status',
      key: 'employee_status',
      className: 'p-4',
      toggleable: true,
      defaultVisible: true,
      render: (row: any) => {
        const status = row.employee_status?.toLowerCase() || '';

        const statusConfig: Record<string, { label: string; className: string }> = {
          active:            { label: 'Active',           className: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 ring-1 ring-green-600/20' },
          'newly_hired':     { label: 'Newly Hired',      className: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 ring-1 ring-green-600/20' },
          terminated:        { label: 'Terminated',       className: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 ring-1 ring-red-600/20' },
          resigned:          { label: 'Resigned',         className: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 ring-1 ring-yellow-600/20' },
          awol:              { label: 'AWOL',             className: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 ring-1 ring-orange-600/20' },
          'end_of_contract': { label: 'End of Contract',  className: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 ring-1 ring-orange-600/20' },
        };

        const config = statusConfig[status] ?? {
          label: row.employee_status || 'Unknown',
          className: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 ring-1 ring-slate-500/20',
        };

        return (
          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.className}`}>
            {config.label}
          </span>
        );
      }
    },
    {
      label: '',
      key: 'actions',
      isAction: true,
      toggleable: false,
      className: 'p-4 text-center'
    },
  ],
  actions: [
    {
      label: 'View',
      icon: 'Eye',
      onClick: 'onView',
      className: 'bg-transparent hover:bg-transparent text-gray-600 hover:text-gray-900 cursor-pointer'
    },
    {
      label: 'Edit',
      icon: 'Pencil',
      onClick: 'onEdit',
      className: 'bg-transparent hover:bg-transparent text-gray-600 hover:text-gray-900 cursor-pointer'
    },
    {
      label: 'Delete',
      icon: 'Trash',
      onClick: 'onDelete',
      className: 'bg-transparent hover:bg-transparent text-gray-600 hover:text-gray-900 cursor-pointer'
    },
  ],
};

function formatDate(dateString: string | undefined | null): string {
  if (!dateString) return '';
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  } catch {
    return '';
  }
}