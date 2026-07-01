package com.rentalmanagement.app.models;

public class DashboardMetricItem {

    private final String label;
    private final String value;
    private final String icon;

    public DashboardMetricItem(String label, String value, String icon) {
        this.label = label;
        this.value = value;
        this.icon = icon;
    }

    public String getLabel() { return label; }
    public String getValue() { return value; }
    public String getIcon() { return icon; }
}
