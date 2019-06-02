import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("unit"))
export class Unit extends Model<Unit> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public unit_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public unit_name: string;
}
