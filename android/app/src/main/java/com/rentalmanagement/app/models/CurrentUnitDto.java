package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class CurrentUnitDto {

    @SerializedName("unit_number")
    private String unitNumber;

    @SerializedName("property_name")
    private String propertyName;

    @SerializedName("rent_amount")
    private Double rentAmount;

    public String getUnitNumber() { return unitNumber; }
    public String getPropertyName() { return propertyName; }
    public Double getRentAmount() { return rentAmount; }
}
