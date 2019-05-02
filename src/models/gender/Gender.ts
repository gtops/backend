import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("gender"))
export class Gender extends Model<Gender> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public gender_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public gender: string;
}
