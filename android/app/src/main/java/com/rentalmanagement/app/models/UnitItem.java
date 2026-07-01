package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class UnitItem {

    @SerializedName("id")
    private Integer id;

    @SerializedName("unit_number")
    private String unitNumber;

    @SerializedName("rent_amount")
    private Double rentAmount;

    @SerializedName("is_occupied")
    private Boolean occupied;

    @SerializedName("property")
    private PropertyLite property;

    public Integer getId() {
        return id;
    }

    public String getUnitNumber() {
        return unitNumber;
    }

    public Double getRentAmount() {
        return rentAmount;
    }

    public Boolean getOccupied() {
        return occupied;
    }

    public PropertyLite getProperty() {
        return property;
    }
}
