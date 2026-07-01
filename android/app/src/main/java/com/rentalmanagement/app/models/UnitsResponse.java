package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class UnitsResponse {

    @SerializedName("units")
    private List<UnitItem> units;

    public List<UnitItem> getUnits() {
        return units;
    }
}
