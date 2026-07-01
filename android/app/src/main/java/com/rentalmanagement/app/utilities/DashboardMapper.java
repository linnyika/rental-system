package com.rentalmanagement.app.utilities;

import com.rentalmanagement.app.models.DashboardActionItem;
import com.rentalmanagement.app.models.DashboardActivityDto;
import com.rentalmanagement.app.models.DashboardListItem;
import com.rentalmanagement.app.models.DashboardMetricItem;
import com.rentalmanagement.app.models.DashboardPaymentDto;
import com.rentalmanagement.app.models.DashboardRequestDto;
import com.rentalmanagement.app.models.DashboardStats;
import com.rentalmanagement.app.models.DashboardTaskDto;
import com.rentalmanagement.app.models.DashboardUserDto;
import com.rentalmanagement.app.models.PropertyItem;
import com.rentalmanagement.app.models.UnitItem;

import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

public final class DashboardMapper {

    private DashboardMapper() {
    }

    public static List<DashboardMetricItem> adminMetrics(DashboardStats stats) {
        List<DashboardMetricItem> items = new ArrayList<>();
        items.add(new DashboardMetricItem("Total Properties", value(stats == null ? null : stats.getTotalProperties()), "P"));
        items.add(new DashboardMetricItem("Total Landlords", value(stats == null ? null : stats.getTotalLandlords()), "L"));
        items.add(new DashboardMetricItem("Total Tenants", value(stats == null ? null : stats.getTotalTenants()), "T"));
        items.add(new DashboardMetricItem("Total Caretakers", value(stats == null ? null : stats.getTotalCaretakers()), "C"));
        return items;
    }

    public static List<DashboardMetricItem> landlordMetrics(DashboardStats stats) {
        List<DashboardMetricItem> items = new ArrayList<>();
        items.add(new DashboardMetricItem("Total Properties", value(stats == null ? null : stats.getTotalProperties()), "P"));
        items.add(new DashboardMetricItem("Total Units", value(stats == null ? null : stats.getTotalUnits()), "U"));
        items.add(new DashboardMetricItem("Occupied Units", value(stats == null ? null : stats.getOccupiedUnits()), "O"));
        items.add(new DashboardMetricItem("Vacant Units", value(stats == null ? null : stats.getVacantUnits()), "V"));
        items.add(new DashboardMetricItem("Pending Maintenance", value(stats == null ? null : stats.getPendingMaintenanceRequests()), "M"));
        return items;
    }

    public static List<DashboardMetricItem> caretakerMetrics(DashboardStats stats) {
        List<DashboardMetricItem> items = new ArrayList<>();
        items.add(new DashboardMetricItem("Assigned Tasks", value(stats == null ? null : stats.getAssignedTasks()), "A"));
        items.add(new DashboardMetricItem("In Progress", value(stats == null ? null : stats.getTasksInProgress()), "P"));
        items.add(new DashboardMetricItem("Completed", value(stats == null ? null : stats.getCompletedTasks()), "D"));
        items.add(new DashboardMetricItem("Activity Logs", value(stats == null ? null : stats.getActivityLogs()), "L"));
        return items;
    }

    public static List<DashboardMetricItem> tenantMetrics(DashboardStats stats) {
        List<DashboardMetricItem> items = new ArrayList<>();
        items.add(new DashboardMetricItem("Total Paid", "KES " + money(stats == null ? null : stats.getTotalPaid()), "P"));
        items.add(new DashboardMetricItem("Verified", value(stats == null ? null : stats.getVerifiedPayments()), "V"));
        items.add(new DashboardMetricItem("Pending", value(stats == null ? null : stats.getPendingPayments()), "R"));
        int totalPayments = (stats == null || stats.getVerifiedPayments() == null ? 0 : stats.getVerifiedPayments())
                + (stats == null || stats.getPendingPayments() == null ? 0 : stats.getPendingPayments());
        items.add(new DashboardMetricItem("Payments", String.valueOf(totalPayments), "S"));
        return items;
    }

    public static List<DashboardActionItem> adminActions() {
        List<DashboardActionItem> items = new ArrayList<>();
        items.add(new DashboardActionItem("Manage Landlords", "View registered landlords", "M"));
        return items;
    }

    public static List<DashboardActionItem> landlordActions() {
        List<DashboardActionItem> items = new ArrayList<>();
        items.add(new DashboardActionItem("Properties", "View your properties", "P"));
        items.add(new DashboardActionItem("Units", "Manage units by property", "U"));
        items.add(new DashboardActionItem("Register Tenant", "Assign tenant to unit", "T"));
        items.add(new DashboardActionItem("Register Caretaker", "Create caretaker account", "C"));
        items.add(new DashboardActionItem("Maintenance", "Review requests", "M"));
        items.add(new DashboardActionItem("Notifications", "View alerts", "N"));
        items.add(new DashboardActionItem("Payments", "Review rent payments", "R"));
        items.add(new DashboardActionItem("Reports", "Open reports", "A"));
        return items;
    }

    public static List<DashboardActionItem> caretakerActions() {
        List<DashboardActionItem> items = new ArrayList<>();
        items.add(new DashboardActionItem("Activity Logs", "See recent activity", "L"));
        items.add(new DashboardActionItem("Notifications", "View alerts", "N"));
        return items;
    }

    public static List<DashboardActionItem> tenantActions() {
        List<DashboardActionItem> items = new ArrayList<>();
        items.add(new DashboardActionItem("Pay Rent", "Open payment flow", "P"));
        items.add(new DashboardActionItem("Request Maintenance", "Submit issue", "M"));
        items.add(new DashboardActionItem("Payment History", "Review payments", "H"));
        items.add(new DashboardActionItem("Notifications", "Open alerts", "N"));
        return items;
    }

    public static List<DashboardListItem> recentRegistrations(List<DashboardUserDto> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }
        for (DashboardUserDto item : items) {
            if (item == null) {
                continue;
            }
            results.add(new DashboardListItem(
                    safe(item.getName()),
                    safe(item.getContact()),
                    capitalize(item.getRole()),
                    safe(item.getCreatedAt())
            ));
        }
        return results;
    }

    public static List<DashboardListItem> recentPayments(List<DashboardPaymentDto> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }
        for (DashboardPaymentDto item : items) {
            if (item == null) {
                continue;
            }
            results.add(new DashboardListItem(
                    "KES " + money(item.getAmount()),
                    safe(item.getTenantName()) + " - " + safe(item.getPropertyName()) + " / " + safe(item.getUnitNumber()),
                    capitalize(item.getStatus()),
                    safe(item.getPaymentDate()) + " - " + safe(item.getMethod())
            ));
        }
        return results;
    }

    public static List<DashboardListItem> maintenanceRequests(List<DashboardRequestDto> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }
        for (DashboardRequestDto item : items) {
            if (item == null) {
                continue;
            }
            results.add(new DashboardListItem(
                    safe(item.getDescription()),
                    safe(item.getTenantName()) + " - " + safe(item.getPropertyName()) + " / " + safe(item.getUnitNumber()),
                    capitalize(item.getStatus()),
                    safe(item.getCreatedAt())
            ));
        }
        return results;
    }

    public static List<DashboardListItem> tasks(List<DashboardTaskDto> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }
        for (DashboardTaskDto item : items) {
            if (item == null) {
                continue;
            }
            results.add(new DashboardListItem(
                    safe(item.getDescription()),
                    safe(item.getTenantName()) + " - " + safe(item.getPropertyName()) + " / " + safe(item.getUnitNumber()),
                    capitalize(item.getStatus()),
                    safe(item.getCreatedAt())
            ));
        }
        return results;
    }

    public static List<DashboardListItem> activityLogs(List<DashboardActivityDto> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }
        for (DashboardActivityDto item : items) {
            if (item == null) {
                continue;
            }
            results.add(new DashboardListItem(
                    safe(item.getDescription()),
                    "",
                    "Log",
                    safe(item.getActivityDate())
            ));
        }
        return results;
    }

    public static List<DashboardListItem> notifications(List<com.rentalmanagement.app.models.NotificationItem> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }
        for (com.rentalmanagement.app.models.NotificationItem item : items) {
            if (item == null) {
                continue;
            }
            results.add(new DashboardListItem(
                    safe(item.getTitle()),
                    safe(item.getMessage()),
                    item.isRead() ? "Read" : "Unread",
                    safe(item.getCreatedAt())
            ));
        }
        return results;
    }

    public static List<DashboardListItem> properties(List<PropertyItem> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }

        for (PropertyItem item : items) {
            if (item == null) {
                continue;
            }

            results.add(new DashboardListItem(
                    safe(item.getName()),
                    safe(item.getAddress()),
                    "Property",
                    "ID: " + value(item.getId())
            ));
        }
        return results;
    }

    public static List<DashboardListItem> units(List<UnitItem> items) {
        List<DashboardListItem> results = new ArrayList<>();
        if (items == null) {
            return results;
        }

        for (UnitItem item : items) {
            if (item == null) {
                continue;
            }

            long rent = Math.round(item.getRentAmount() == null ? 0 : item.getRentAmount());
            boolean occupied = item.getOccupied() != null && item.getOccupied();

            results.add(new DashboardListItem(
                    "Unit " + safe(item.getUnitNumber()),
                    "Rent: KES " + rent,
                    occupied ? "Occupied" : "Available",
                    "ID: " + value(item.getId())
            ));
        }
        return results;
    }

    public static String currentUnitTitle(String unitNumber, String propertyName) {
        return "Unit " + safe(unitNumber) + " - " + safe(propertyName);
    }

    private static String value(Integer value) {
        return String.valueOf(value == null ? 0 : value);
    }

    private static String money(Double value) {
        return String.valueOf(Math.round(value == null ? 0 : value));
    }

    private static String safe(String value) {
        return value == null || value.trim().isEmpty() ? "-" : value;
    }

    private static String capitalize(String value) {
        if (value == null || value.trim().isEmpty()) {
            return "-";
        }
        String normalized = value.replace("_", " ");
        return normalized.substring(0, 1).toUpperCase(Locale.ROOT) + normalized.substring(1);
    }
}
