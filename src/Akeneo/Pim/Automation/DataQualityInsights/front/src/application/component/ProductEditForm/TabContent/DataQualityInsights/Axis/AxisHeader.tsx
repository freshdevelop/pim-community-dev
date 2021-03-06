import React, {FC} from 'react';
import Rate from '../../../../Rate';
import AllAttributesLink from '../AllAttributesLink';
import {CriterionEvaluationResult, Evaluation} from '../../../../../../domain';
import {uniq as _uniq} from 'lodash';

const __ = require('oro/translator');

const getAxisAttributesWithRecommendations = (criteria: CriterionEvaluationResult[]): string[] => {
  let attributes: string[] = [];

  criteria.map(criterion => {
    attributes = [...criterion.improvable_attributes, ...attributes];
  });

  return _uniq(attributes);
};

const canDisplayAllAttributesLink = (attributes: string[]) => {
  return attributes.length > 0;
};

type Props = {
  evaluation: Evaluation;
  axis: string;
  showRate: boolean;
};

const AxisHeader: FC<Props> = ({evaluation, axis, showRate}) => {
  const {criteria, rate} = evaluation;
  const allAttributes = getAxisAttributesWithRecommendations(criteria);

  return (
    <header className="AknSubsection-title">
      <span className="group-label">
        <span className="AxisEvaluationTitle">
          {__(`akeneo_data_quality_insights.product_evaluation.axis.${axis}.title`)}
        </span>
        {showRate && <Rate value={rate ? rate.rank : null} />}
      </span>
      <span>
        {canDisplayAllAttributesLink(allAttributes) && <AllAttributesLink axis={axis} attributes={allAttributes} />}
      </span>
    </header>
  );
};

export {AxisHeader};
