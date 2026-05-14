// components/employee/employee-filter-bar.tsx

import { X } from 'lucide-react';
import { useMemo } from 'react';
import {
    SearchInput,
    MultiSelectPopover,
    SingleSelectPopover,
    DateRangePicker,
} from '@/components/filters/filter-primitives';
import { cn } from '@/lib/utils';

// ─── Employee status enum (mirrors DB) ────────────────────────────────────────
export const EMPLOYEE_STATUS_OPTIONS = [
    { value: 'active',          label: 'Active' },
    { value: 'end_of_contract', label: 'End of Contract' },
    { value: 'awol',            label: 'AWOL' },
    { value: 'terminated',      label: 'Terminated' },
    { value: 'resigned',        label: 'Resigned' },
    { value: 'newly_hired',     label: 'Newly Hired' },
] as const;

export type EmployeeStatusValue = typeof EMPLOYEE_STATUS_OPTIONS[number]['value'] | '';

// ─── Types ────────────────────────────────────────────────────────────────────
export interface BranchData {
    id: number;
    branch_name: string;
    branch_address: string;
    sites: Array<{ id: number; site_name: string }>;
}

export interface FilterConfig {
    search?: boolean;
    position?: boolean;
    branch?: boolean;
    site?: boolean;
    date?: boolean;
    status?: boolean;
}

interface EmployeeFilterBarProps {
    // Data
    allPositions?: string[];
    allBranches?: string[];
    allSites?: string[];
    branchesData?: BranchData[];

    // Filter values
    searchTerm?: string;
    selectedPositions?: string[];
    selectedBranch?: string;
    selectedSite?: string;
    /** Single employee_status enum value, or '' for "all" */
    status?: string;
    dateFrom?: Date | undefined;
    dateTo?: Date | undefined;

    // Change handlers
    onSearchChange?: (value: string) => void;
    onPositionsChange?: (positions: string[]) => void;
    onBranchChange?: (branch: string) => void;
    onSiteChange?: (site: string) => void;
    /** Called with the selected enum value, or '' when cleared */
    onStatusChange?: (status: string) => void;
    onDateFromChange?: (date: Date | undefined) => void;
    onDateToChange?: (date: Date | undefined) => void;

    // Clear all
    onClearAll?: () => void;

    // Configuration
    filters?: FilterConfig;
    searchPlaceholder?: string;
    dateLabel?: string;
}

// ─── Component ────────────────────────────────────────────────────────────────
export function EmployeeFilterBar({
    allPositions = [],
    allBranches = [],
    allSites = [],
    branchesData = [],
    searchTerm = '',
    selectedPositions = [],
    selectedBranch,
    selectedSite,
    status = '',
    dateFrom,
    dateTo,
    onSearchChange,
    onPositionsChange,
    onBranchChange,
    onSiteChange,
    onStatusChange,
    onDateFromChange,
    onDateToChange,
    onClearAll,
    filters = {
        search: true,
        position: true,
        branch: true,
        site: true,
        date: true,
        status: true,
    },
    searchPlaceholder = 'Search...',
    dateLabel = 'Date',
}: EmployeeFilterBarProps) {

    // Branch options
    const branchOptions = useMemo(() => {
        if (allBranches.length > 0) return allBranches.map(b => ({ value: b, label: b }));
        return branchesData.map(b => ({ value: b.branch_name, label: b.branch_name }));
    }, [allBranches, branchesData]);

    // Site options based on selected branch
    const siteOptions = useMemo(() => {
        if (!selectedBranch) return [];
        const branch = branchesData.find(b => b.branch_name === selectedBranch);
        if (branch?.sites?.length) return branch.sites.map(s => ({ value: s.site_name, label: s.site_name }));
        if (allSites.length > 0) return allSites.map(s => ({ value: s, label: s }));
        return [];
    }, [branchesData, selectedBranch, allSites]);

    // All 6 enum statuses — always shown, no prop needed
    const statusOptions = EMPLOYEE_STATUS_OPTIONS.map(o => ({ value: o.value, label: o.label }));

    // Active filter detection
    const hasActiveFilters = !!(
        (filters.search   && searchTerm?.trim())       ||
        (filters.position && selectedPositions.length)  ||
        (filters.branch   && selectedBranch)            ||
        (filters.site     && selectedSite)              ||
        (filters.status   && status)                    ||
        (filters.date     && (dateFrom || dateTo))
    );

    const activeFilterCount = [
        filters.search   && searchTerm?.trim(),
        filters.position && selectedPositions.length,
        filters.branch   && selectedBranch,
        filters.site     && selectedSite,
        filters.status   && status,
        filters.date     && (dateFrom || dateTo),
    ].filter(Boolean).length;

    const hasOtherFilters =
        filters.position || filters.branch || filters.site || filters.date || filters.status;

    return (
        <div className="flex items-center gap-2 flex-wrap">
            {/* Search */}
            {filters.search && onSearchChange && (
                <SearchInput
                    value={searchTerm}
                    onChange={onSearchChange}
                    placeholder={searchPlaceholder}
                />
            )}

            {/* Divider */}
            {filters.search && hasOtherFilters && (
                <div className="w-px h-5 bg-slate-200 dark:bg-slate-700 flex-shrink-0 mx-0.5" />
            )}

            {/* Filter pills */}
            <div className="flex items-center gap-1.5 flex-wrap">
                {filters.position && onPositionsChange && (
                    <MultiSelectPopover
                        label="Position"
                        options={allPositions}
                        selected={selectedPositions}
                        onChange={onPositionsChange}
                    />
                )}

                {filters.branch && onBranchChange && (
                    <SingleSelectPopover
                        label="Branch"
                        options={branchOptions}
                        value={selectedBranch}
                        onChange={onBranchChange}
                        placeholder="Branch"
                    />
                )}

                {filters.site && selectedBranch && onSiteChange && siteOptions.length > 0 && (
                    <SingleSelectPopover
                        label="Site"
                        options={siteOptions}
                        value={selectedSite}
                        onChange={onSiteChange}
                        placeholder="Site"
                    />
                )}

                {filters.date && onDateFromChange && onDateToChange && (
                    <DateRangePicker
                        label={dateLabel}
                        dateFrom={dateFrom}
                        dateTo={dateTo}
                        onFromChange={onDateFromChange}
                        onToChange={onDateToChange}
                        placeholder={dateLabel}
                    />
                )}

                {/* Status — single-select, all 6 enum values always present */}
                {filters.status && onStatusChange && (
                    <SingleSelectPopover
                        label="Status"
                        options={statusOptions}
                        value={status}
                        onChange={onStatusChange}
                        placeholder="Status"
                    />
                )}
            </div>

            {/* Clear all */}
            {hasActiveFilters && onClearAll && (
                <button
                    onClick={onClearAll}
                    className={cn(
                        'inline-flex items-center gap-1 h-9 px-2.5 rounded-lg text-xs font-semibold',
                        'text-slate-400 dark:text-slate-500',
                        'hover:text-[#d85e39] dark:hover:text-orange-400',
                        'hover:bg-[#d85e39]/8 dark:hover:bg-[#d85e39]/15',
                        'border border-transparent hover:border-[#d85e39]/20',
                        'transition-all duration-150',
                    )}
                >
                    <X className="h-3.5 w-3.5" />
                    Clear {activeFilterCount > 0 && `(${activeFilterCount})`}
                </button>
            )}
        </div>
    );
}